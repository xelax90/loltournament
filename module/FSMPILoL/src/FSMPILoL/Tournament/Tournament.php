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

/**
 * Description of Tournament
 *
 * @author schurix
 */
class Tournament implements ServiceLocatorAwareInterface, TournamentAwareInterface{
	use ServiceLocatorAwareTrait, TournamentAwareTrait;
	
	protected $api;
	protected $em;
	protected $summoners;
	
	protected function getSummonerCacheKey(){
		$anmeldungen = $this->getTournament()->getAnmeldungen();
		$maxAnmeldungId = 0;
		foreach($anmeldungen as $anmeldung){
			$maxAnmeldungId = max($maxAnmeldungId, $anmeldung->getId());
		}
		return $maxAnmeldungId;
	}
	
	public function getAPI(){
		if(null === $this->api){
			$this->api = $this->getServiceLocator()->get(RiotAPI::class);
		}
		return $this->api;
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
}
