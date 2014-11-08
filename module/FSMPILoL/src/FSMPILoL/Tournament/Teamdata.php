<?php
namespace FSMPILoL\Tournament;

class Teamdata{
	protected $team;
	protected $points;
	protected $buchholz;
	protected $playedHome;
	protected $playedGuest;
	protected $previousGameHome;
	protected $penultimateGameHome;
	
	public function __construct(Teamdata $data = null){
		if(!empty($data)){
			$this->team = $data->getTeam();
			$this->points = $data->getPoints();
			$this->buchholz = $data->getBuchholz();
			$this->playedHome = $data->getPlayedHome();
			$this->playedGuest = $data->getPlayedGuest();
			$this->previousGameHome = $data->getPreviousGameHome();
			$this->penultimateGameHome = $data->getPenultimateGameHome();
		}
	}
	
	public function getTeam(){
		return $this->team;
	}
	
	public function setTeam($team){
		return $this->team = $team;
	}
	
	public function getPoints(){
		return $this->points;
	}

	public function setPoints($points){
		$this->points = $points;
	}

	public function getBuchholz(){
		return $this->buchholz;
	}

	public function setBuchholz($buchholz){
		$this->buchholz = $buchholz;
	}

	public function getPlayedHome(){
		return $this->playedHome;
	}

	public function setPlayedHome($playedHome){
		$this->playedHome = $playedHome;
	}

	public function getPlayedGuest(){
		return $this->playedGuest;
	}

	public function setPlayedGuest($playedGuest){
		$this->playedGuest = $playedGuest;
	}

	public function getPreviousGameHome(){
		return $this->previousGameHome;
	}

	public function setPreviousGameHome($previousGameHome){
		$this->previousGameHome = $previousGameHome;
	}

	public function getPenultimateGameHome(){
		return $this->penultimateGameHome;
	}

	public function setPenultimateGameHome($penultimateGameHome){
		$this->penultimateGameHome = $penultimateGameHome;
	}
	
	public function getHochgereiht(){
		return $this->hochgereiht;
	}

	public function setHochgereiht($hochgereiht){
		$this->hochgereiht = $hochgereiht;
	}

	public function getRuntergereiht(){
		return $this->runtergereiht;
	}

	public function setRuntergereiht($runtergereiht){
		$this->runtergereiht = $runtergereiht;
	}
		
	public function isFloater(){
		return $this->getRuntergereiht() || $this->getHochgereiht();
	}
	
	public function getFarbverteilung(){
		return $this->getPlayedHome() - $this->getPlayedGuest();
	}
	
	public function getFarberwartung(){
		if($this->getFarbverteilung() < -1)
			return "+h";
		if($this->getFarbverteilung() > 1)
			return "+g";
		
		if($this->previousGameHome === false && $this->penultimateGameHome === false)
			return "+h";
		if($this->previousGameHome === true && $this->penultimateGameHome === true)
			return "+g";
		
		if($this->getFarbverteilung() < 0)
			return "h";
		if($this->getFarbverteilung() > 0)
			return "g";
		
		if($this->previousGameHome === false)
			return "+o";
		if($this->previousGameHome === true)
			return "-o";
		
		return "o";
	}
	
	
}