<?php
namespace FSMPILoL\Tournament;

use FSMPILoL\Entity\Tournament;
use Doctrine\ORM\EntityManager;
class Anmeldung{
	
	/**
	 * @var EntityManager
	 */
	protected $em;
	
	/**
	 * @var Tournament
	 */
	protected $tournament;
	
	public function getEntityManager(){
		return $this->em;
	}
	
	public function getTournament(){
		return $this->tournament;
	}
	
	public function __construct(Tournament $tournament, EntityManager $em){
		$this->tournament = $tournament;
		$this->em = $em;
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
	
	
}
