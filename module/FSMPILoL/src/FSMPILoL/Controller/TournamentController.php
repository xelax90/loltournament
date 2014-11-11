<?php
namespace FSMPILoL\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FSMPILoL\Riot\RiotAPI;
use FSMPILoL\Tournament\Summonerdata;

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
		$tournament = $this->getTournament();
		$summoners = $this->getSummoners();
		$api = $this->getAPI();
		$anmeldungen = $tournament->getAnmeldungen();
		foreach($anmeldungen as $anmeldung){
			$standardname = RiotAPI::getStandardName($anmeldung->getSummonerName());
			$summoner = $summoners[$standardname];
			$anmeldung->setSummonerdata(new Summonerdata($api, $anmeldung, $summoner));
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
			$group->setTeamdata();
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
			$group->setTeamdata();
		}
		
		$this->setAPIData();
		
		//$api = new RiotAPI($this->getServiceLocator());
		return new ViewModel(array('tournament' => $tournament));
	}
}
