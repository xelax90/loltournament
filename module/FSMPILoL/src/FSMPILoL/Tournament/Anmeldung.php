<?php
namespace FSMPILoL\Tournament;

use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use FSMPILoL\Tournament\TournamentAwareInterface;
use FSMPILoL\Tournament\TournamentAwareTrait;
use FSMPILoL\Options\AnmeldungOptions;

class Anmeldung implements ServiceLocatorAwareInterface, TournamentAwareInterface{
	use ServiceLocatorAwareTrait, TournamentAwareTrait;
	
	protected $config;
	protected $api;
	
	public function getEntityManager(){
		if (null === $this->entityManager) {
			$this->entityManager = $this->getServiceLocator()->get(EntityManager::class);
		}
		return $this->entityManager;
	}

	protected function getConfig(){
		if (null === $this->config) {
			$this->config = $this->getServiceLocator()->get(AnmeldungOptions::class);
		}
		return $this->config;
	}
	
	public function getAPI(){
		if(null === $this->api){
			$this->api = $this->getServiceLocator()->get(RiotAPI::class);
		}
		return $this->api;
	}
	
	public function getTeams(){
		$result = array();
		
		$anmeldungen = $this->getAll();
		foreach($anmeldungen as $anmeldung){
			if(!$anmeldung->getTeamName())
				continue;
			$result[$anmeldung->getTeamName()][] = $anmeldung;
		}
		
		return $result;
	}
	
	public function getSingles(){
		$result = array();
		
		$anmeldungen = $this->getAll();
		foreach($anmeldungen as $anmeldung){
			if($anmeldung->getTeamName())
				continue;
			$result[] = $anmeldung;
		}
		
		return $result;
	}
	
	public function getAll(){
		return $this->getTournament()->getAnmeldungen();
	}
	
	public function getAvailableIcons(){
		$files = scandir($this->getConfig()->getIconDir());
		$icons = array();
		foreach($files as $file){
			if(in_array(pathinfo($file, PATHINFO_EXTENSION), array('jpg', 'png'))){
				$icons[] = $file;
			}
		}
		
		$used = array();
		foreach($this->getAll() as $anmeldung){
			if ($anmeldung->getIcon()) {
				$used[] = $anmeldung->getIcon();
			}
		}
		
		$tournament = $this->getTournament();
		foreach($tournament->getGroups() as $group){
			foreach($group->getTeams() as $team){
				$used[] = $team->getIcon();
			}
		}
		
		return array_diff($icons, $used);
	}
	
	public function setAPIData(){
		$api = $this->getAPI();
		$anmeldungen = $this->getAll();
		
		$summonerdata = array();
		
		$cache = $this->getServiceLocator()->get('FSMPILoL\SummonerdataCache');
		$cacheKey = $this->getSummonerCacheKey();
		if($cache->hasItem($cacheKey) && (!$cache->itemHasExpired($cacheKey))){
			$summonerdata = unserialize($cache->getItem($cacheKey));
		} else {
			$summoners = $this->getTournamentSummoners();
			foreach($anmeldungen as $anmeldung){
				/* @var $anmeldung FSMPILoL\Entity\Anmeldung */
				$standardname = RiotAPI::getStandardName($anmeldung->getSummonerName());
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
