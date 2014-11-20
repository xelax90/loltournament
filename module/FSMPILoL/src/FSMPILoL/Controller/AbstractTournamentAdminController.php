<?php

namespace FSMPILoL\Controller;

use FSMPILoL\Riot\RiotAPi;
use FSMPILoL\Tournament\Group;
use FSMPILoL\Tournament\Summonerdata;

/**
 * Description of AbstractTournamentAdminController
 *
 * @author schurix
 */
class AbstractTournamentAdminController extends AbstractAdminController{
	/** @var array */
	protected $summoners;
	
	/** @var Tournament */
	protected $tournament;

	/** @var RiotAPI */
	protected $api;
	
	public function getTournament(){
		if(null === $this->tournament){
			$options = $this->getServiceLocator()->get('FSMPILoL\Options\Anmeldung');
			$tournamentId = $options->getTournamentId();
			$em = $this->getEntityManager();
			$this->tournament = $em->getRepository('FSMPILoL\Entity\Tournament')->find($tournamentId);
		}
		return $this->tournament;
	}
	
	public function getSummoners(){
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
	
	public function getAPI(){
		if(null === $this->api){
			$this->api = new RiotAPI($this->getServiceLocator());
		}
		return $this->api;
	}
	
	protected function setTeamdata(){
		$tournament = $this->getTournament();
		if(!$tournament)
		 	return;
		
		foreach($tournament->getGroups() as $group){
			$gGroup = new Group($group, $this->getServiceLocator());
			$gGroup->setTeamdata();
		}
	}
	
	protected function getSummonerCacheKey(){
		$anmeldungen = $this->getTournament()->getAnmeldungen();
		$maxAnmeldungId = 0;
		foreach($anmeldungen as $anmeldung){
			$maxAnmeldungId = max($maxAnmeldungId, $anmeldung->getId());
		}
		return $maxAnmeldungId;
	}
	
	protected function setAPIData(){
		$tournament = $this->getTournament();
		$api = $this->getAPI();
		$anmeldungen = $tournament->getAnmeldungen();
		
		$summonerdata = array();
		
		$cache = $this->getServiceLocator()->get('FSMPILoL\SummonerdataCache');
		$cacheKey = $this->getSummonerCacheKey();
		
		if($cache->hasItem($cacheKey) && !$cache->itemHasExpired($cacheKey)){
			$summonerdata = unserialize($cache->getItem($cacheKey));
		} else {
			$summoners = $this->getSummoners();
			foreach($anmeldungen as $anmeldung){
				$standardname = RiotAPI::getStandardName($anmeldung->getSummonerName());
				$summoner = $summoners[$standardname];
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
