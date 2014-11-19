<?php
namespace FSMPILoL\Tournament\RoundCreator;

use Doctrine\Collection\ArrayCollection;
use FSMPILoL\Tournament\Group;

abstract class AbstractRoundCreator {
	
	protected $tournament;
	protected $group;
	
	protected $globalDefaults = array(
		'gamesPerMatch' => 3,
		'pointsPerGamePoint' => 1,
		'pointsPerMatchWin' => 0,
		'pointsPerMatchDraw' => 0,
		'pointsPerMatchLoss' => 0,
		'pointsPerMatchFree' => 2,
		'ignoreColors' => false
	);
	
	public function __construct(Group $group){
		$this->group = $group;
	}
	
	public function getTournament(){
		if(null === $this->tournament){
			$this->tournament = $group->getGroup()->getTournament();
		}
		return $this->tournament;
	}
	
	public function getGroup(){
		return $this->group;
	}
	
	public function setGroup($group){
		return $this->group = $group;
	}
	
	abstract public function nextRound(\DateTime $startDate, $properties, $isHidden = true, $duration = 14, $timeForDates = 7);
	
	protected function createGamesForMatch($match){
		$properties = $match->getRound()->getProperties();
		
		$games = array();
		for($j = 0; $j < $properties['gamesPerMatch']; $j++){
			$game = new Game();
			if($j % 2 == 0){
				$teamBlue = $match->getTeamHome();
				$teamPurple = $match->getTeamGuest();
			} else {
				$teamBlue = $match->getTeamGuest();
				$teamPurple = $match->getTeamHome();
			}
			$game->setTeamBlue($teamBlue);
			$game->setTeamPurple($teamPurple);
			$game->setNumber($j+1);
			$game->setMatch($match);
			$game->generateTournamentCode();
			$games[] = $game;
		}
		$match->setGames(new ArrayCollection($games));
	}
}
