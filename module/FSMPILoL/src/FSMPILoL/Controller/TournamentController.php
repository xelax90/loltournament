<?php
namespace FSMPILoL\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FSMPILoL\Riot\RiotAPI;
use FSMPILoL\Tournament\Summonerdata;
use FSMPILoL\Tournament\Group;

class TournamentController extends AbstractActionController
{
	protected $summoners;
	protected $tournament;
	protected $api;
	protected $em;
	
	public function getEntityManager(){
		if (null === $this->em) {
			$this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->em;
	}
	
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
			if(!$tournament)
				return null;
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
	
	protected function setAPIData(){
		$start = time();
		$tournament = $this->getTournament();
		$api = $this->getAPI();
		$anmeldungen = $tournament->getAnmeldungen();
		
		$summonerdata = array();
		
		$cache = $this->getServiceLocator()->get('FSMPILoL\SummonerdataCache');
		$maxAnmeldungId = 0;
		foreach($anmeldungen as $anmeldung){
			$maxAnmeldungId = max($maxAnmeldungId, $anmeldung->getId());
		}
		
		if($cache->hasItem($maxAnmeldungId) && !$cache->itemHasExpired($maxAnmeldungId)){
			$summonerdata = unserialize($cache->getItem($maxAnmeldungId));
		} else {
			$summoners = $this->getSummoners();
			foreach($anmeldungen as $anmeldung){
				$standardname = RiotAPI::getStandardName($anmeldung->getSummonerName());
				$summoner = $summoners[$standardname];
				$summonerdata[$anmeldung->getId()] = new Summonerdata($api, $anmeldung, $summoner);
			}
			$cache->addItem($maxAnmeldungId, serialize($summonerdata));
		}
		
		foreach($anmeldungen as $anmeldung){
			$summonerdata[$anmeldung->getId()]->setAnmeldung($anmeldung);
			$anmeldung->setSummonerdata($summonerdata[$anmeldung->getId()]);
		}
	}
	
	public function indexAction(){
		return new ViewModel();
	}
	
	public function ergebnisseAction(){
		$tournament = $this->getTournament();
		if(!$tournament)
			return new ViewModel();
		
		foreach($tournament->getGroups() as $group){
			$gGroup = new Group($group, $this->getServiceLocator());
			$gGroup->setTeamdata();
		}
		
		$this->setAPIData();
		
		//$api = new RiotAPI($this->getServiceLocator());
		return new ViewModel(array('tournament' => $tournament));
	}
	
	public function teamsAction(){
		$tournament = $this->getTournament();
		if(!$tournament)
			return new ViewModel();
		
		$this->setAPIData();
		
		//$api = new RiotAPI($this->getServiceLocator());
		return new ViewModel(array('tournament' => $tournament));
	}
	
	public function paarungenAction(){
		$tournament = $this->getTournament();
		if(!$tournament)
			return new ViewModel();
		
		foreach($tournament->getGroups() as $group){
			$gGroup = new Group($group, $this->getServiceLocator());
			$gGroup->setTeamdata();
		}
		
		$this->setAPIData();
		
		//$api = new RiotAPI($this->getServiceLocator());
		return new ViewModel(array('tournament' => $tournament));
	}
}
