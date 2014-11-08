<?php
namespace FSMPILoL\Tournament\RoundCreator;

abstract class AbstractRoundCreator {
	
	protected $tournament;
	protected $group;
	
	public function __construct($group){
		$this->group = $group
	}
	
	public function getTournament(){
		if(null === $this->tournament){
			$this->tournament = $group->getTournament();
		}
		return $this->tournament;
	}
	
	public function getGroup(){
		return $this->group;
	}
	
	public function setGroup($group){
		return $this->group = $group;
	}
	
	abstract public function nextRound(DateTime $startDate, $properties, $isHidden = true, $duration = 14, $timeForDates = 7);
}
