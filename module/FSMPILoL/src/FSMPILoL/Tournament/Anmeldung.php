<?php
namespace FSMPILoL\Tournament;

use FSMPILoL\Entity\Tournament;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class Anmeldung{
	
	/**
	 * @var EntityManager
	 */
	protected $em;
	
	/**
	 * @var Tournament
	 */
	protected $tournament;
	
	protected $config;
	
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator;
	
	protected function getServiceLocator(){
		return $this->serviceLocator;
	}
	
	public function getEntityManager(){
		if (null === $this->entityManager) {
			$this->entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->entityManager;
	}

	protected function getConfig(){
		if (null === $this->config) {
			$this->config = $this->getServiceLocator()->get('FSMPILoL\Options\Anmeldung');
		}
		return $this->config;
	}
	
	public function getTournament(){
		return $this->tournament;
	}
	
	public function __construct(Tournament $tournament, ServiceLocatorInterface $sl){
		$this->tournament = $tournament;
		$this->serviceLocator = $sl;
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
}
