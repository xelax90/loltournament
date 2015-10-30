<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Tournament;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use FSMPILoL\Riot\RiotAPI;
use FSMPILoL\Options\TournamentOptions;
use Doctrine\ORM\EntityManager;
use FSMPILoL\Entity\Tournament as TournamentEntity;

/**
 * Description of Tournament
 *
 * @author schurix
 */
class Tournament implements ServiceLocatorAwareInterface{
	use ServiceLocatorAwareTrait;
	
	/** @var RiotAPI */
	protected $api;
	
	/** @var EntityManager */
	protected $entityManager;
	
	/** @var array */
	protected $summoners;
	
	/** @var array */
	protected $groups;
	
	/** @var TournamentOptions */
	protected $config;
	
	/** @var TournamentEntity */
	protected $tournament;
	
	/** @var Anmeldung */
	protected $anmeldung;
	
	/** @var TeamMatcher */
	protected $teamMatcher;
	
	/**
	 * TODO improve this using some hashing
	 * @return string
	 */
	protected function getSummonerCacheKey(){
		$anmeldungen = $this->getTournament()->getAnmeldungen();
		$maxAnmeldungId = 0;
		foreach($anmeldungen as $anmeldung){
			$maxAnmeldungId = max($maxAnmeldungId, $anmeldung->getId());
		}
		return $maxAnmeldungId;
	}
	
	/**
	 * @return RiotAPI
	 */
	public function getAPI(){
		if(null === $this->api){
			$this->api = $this->getServiceLocator()->get(RiotAPI::class);
		}
		return $this->api;
	}
	
	/**
	 * @return EntityManager
	 */
	public function getEntityManager(){
		if (null === $this->entityManager) {
			$this->entityManager = $this->getServiceLocator()->get(EntityManager::class);
		}
		return $this->entityManager;
	}
	
	/**
	 * @return TournamentOptions
	 */
	public function getConfig(){
		if(null === $this->config){
			$this->config = $this->getServiceLocator()->get(TournamentOptions::class);
		}
		return $this->config;
	}
	
	/**
	 * @return TournamentEntity
	 */
	public function getTournament(){
		if(null === $this->tournament){
			$config = $this->getConfig();
			$tournamentId = $config->getCurrentTournament();
			$this->tournament = $this->getEntityManager()->getRepository(TournamentEntity::class)->find($tournamentId);
		}
		return $this->tournament;
	}
	
	/**
	 * @return Anmeldung
	 */
	public function getAnmeldung(){
		if(null === $this->anmeldung){
			$this->anmeldung = new Anmeldung();
			$this->anmeldung->setServiceLocator($this->getServiceLocator());
			$this->anmeldung->setTournament($this);
		}
		return $this->anmeldung;
	}
	
	/**
	 * Set tournament used for this service
	 * @param TournamentEntity $tournament
	 */
	public function setTournament(TournamentEntity $tournament){
		$this->tournament = $tournament;
	}
	
	/**
	 * @return array
	 */
	public function getTournamentSummoners(){
		if(null === $this->summoners){
			$tournament = $this->getTournament();
			if(!$tournament){
				return null;
			}
			$anmeldungen = $tournament->getAnmeldungen();
			$api = $this->getAPI();
			$this->summoners = $api->getSummoners($anmeldungen);
		}
		return $this->summoners;
	}
	
	/**
	 * Injects Riot API Summonerdata into anmeldungen
	 * @return void
	 */
	public function setAPIData(){
		$tournament = $this->getTournament();
		$api = $this->getAPI();
		$anmeldungen = $tournament->getAnmeldungen();
		
		$summonerdata = array();
		
		/* @var $cache \Zend\Cache\Storage\Adapter\Filesystem */
		$cache = $this->getServiceLocator()->get('FSMPILoL\SummonerdataCache');
		$cacheKey = $this->getSummonerCacheKey();
		if($cache->hasItem($cacheKey) && (!$cache->itemHasExpired($cacheKey))){
			$summonerdata = unserialize($cache->getItem($cacheKey));
		} else {
			$summoners = $this->getTournamentSummoners();
			foreach($anmeldungen as $anmeldung){
				/* @var $anmeldung FSMPILoL\Entity\Anmeldung */
				$standardname = RiotAPI::getStandardName($anmeldung->getSummonerName());
				$summoner = null;
				if(!empty($summoners[$standardname])){
					$summoner = $summoners[$standardname];
				} elseif(!empty($anmeldung->getPlayer()) && !empty($summoners[$anmeldung->getPlayer()->getSummonerId()])) {
					$summoner = $summoners[$anmeldung->getPlayer()->getSummonerId()];
				}
				$summonerdata[$anmeldung->getId()] = new Summonerdata($api, $anmeldung, $summoner);
			}
			$cache->addItem($cacheKey, serialize($summonerdata));
		}
		
		foreach($anmeldungen as $anmeldung){
			$summonerdata[$anmeldung->getId()]->setAnmeldung($anmeldung);
			$anmeldung->setSummonerdata($summonerdata[$anmeldung->getId()]);
		}
	}
	
	/**
	 * Injects team data into teams
	 * @return void
	 */
	public function setTeamdata(){
		$groups = $this->getGroups();
		
		foreach($groups as $group){
			/* @var $group Group */
			$group->setTeamdata();
		}
	}
	
	/**
	 * Returns array ouf groups
	 * @return array
	 */
	public function getGroups(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return array();
		}
		foreach($tournament->getGroups() as $group){
			/* @var $group \FSMPILoL\Entity\Group */
			if(!isset($this->groups[$group->getId()])){
				$gGroup = new Group();
				$gGroup->setServiceLocator($this->getServiceLocator());
				$gGroup->setTournament($this);
				$gGroup->setGroup($group);
				$this->groups[$group->getId()] = $gGroup;
			}
		}
		return $this->groups;
	}
	
	/**
	 * @return TeamMatcher
	 */
	public function getTeamMatcher(){
		if(null === $this->teamMatcher){
			$matcher = new TeamMatcher();
			$matcher->setServiceLocator($this->getServiceLocator());
			$matcher->setTournament($this);
			$this->teamMatcher = $matcher;
		}
		return $this->teamMatcher;
	}
}
