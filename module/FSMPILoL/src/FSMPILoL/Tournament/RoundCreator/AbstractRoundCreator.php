<?php
namespace FSMPILoL\Tournament\RoundCreator;

use Doctrine\Collection\ArrayCollection;

abstract class AbstractRoundCreator {
	
	protected $tournament;
	protected $group;
	
	protected $globalDefaults = array(
		'gamesPerMatch' => 3,
		'pointsPerGamePoint' => 1,
		'pointsPerMatchWin' => 0,
		'pointsPerMatchDraw' => 0,
		'pointsPerMatchLoss' => 0,
		'ignoreColors' => false
	)
	
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
	
	protected function getLastFinishedRound(){
		$rounds = $this->getGroup()->getRounds()->toArray();
		// sort rounds by round number in descending order
		usort($rounds, function($r1, $r2){return $r2->getNumber() - $r1->getNumber()});
		
		$new = new DateTime();
		foreach($rounds as $round){
			$date = new DateTime($round->getStartDate());
			$date->modify('+'.$round->getDuration().' days');
			
			if($now <= $date)
				return $round;
		}
		return null;
	}
	
	protected function getTeamdataPerRound(){
		$rounds = $this->getGroup()->getRounds()->toArray();
		$teams = $this->getGroup()->getTeams();
		
		// sort rounds by round number
		usort($rounds, function($r1, $r2){return $r1->getNumber() - $r2->getNumber()});
		
		$result = array();
		
		$previousRoundData = array();
		foreach($teams as $team){
			$previousRoundData[$team->getId()] = new Teamdata();
			$previousRoundData[$team->getId()]->setTeam($team);
		}
		$result[0] = $previousRoundData;
		
		$opponents = array();
		foreach($rounds as $round){
			$roundData = array();
			// points, buchholz, playedHome, playedGuest, previousGameHome, penultimateGameHome
			foreach($round->getMatches() as $match){
				$th = $match->getTeamHome();
				$tg = $match->getTeamGuest();
				
				$opponents[$th->getId()][] = $tg;
				$opponents[$tg->getId()][] = $th;
				
				if(empty($roundData[$th->getId()])){
					$olddata = $previousRoundData[$th->getId()];
					$roundData[$th->getId()] = new Teamdata($olddata);
				}
				
				if(empty($roundData[$tg->getId()])){
					$olddata = $previousRoundData[$tg->getId()];
					$roundData[$tg->getId()] = new Teamdata($olddata);
				}
				
				$pointsHome = 0;
				$pointsGuest = 0;
				$gamesWonHome = 0;
				$gamesWonGuest = 0;
				
				foreach($match->getGames() as $game){
					if($game->getTeamBlue() == $th){
						$pointsHome += $game->getPointsBlue() * $round->getProperties()['pointsPerGame'];
						if($game->getPointsBlue() > $game->getPointsPurple())
							$gamesWonHome++;
					} elseif($game->getTeamPurple() == $th){
						$pointsHome += $game->getPointsPurple() * $round->getProperties()['pointsPerGame'];
						if($game->getPointsPurple() > $game->getPointsBlue())
							$gamesWonHome++;
					}
					
					if($game->getTeamBlue() == $tg){
						$pointsGuest += $game->getPointsBlue() * $round->getProperties()['pointsPerGame'];
						if($game->getPointsBlue() > $game->getPointsPurple())
							$gamesWonGuest++;
					} elseif($game->getTeamPurple() == $tg){
						$pointsGuest += $game->getPointsPurple() * $round->getProperties()['pointsPerGame'];
						if($game->getPointsPurple() > $game->getPointsBlue())
							$gamesWonGuest++;
					}
				}
				
				if($pointsHome > $pointsGuest || ($pointsHome == 0 && $pointsGuest == 0 && $gamesWonHome > $gamesWonGuest)){
					$pointsHome += $round->getProperties()['pointsPerMatchWin'];
					$pointsGuest += $round->getProperties()['pointsPerMatchLoss'];
				} elseif ($pointsGuest > $pointsHome || ($pointsHome == 0 && $pointsGuest == 0 && $gamesWonGuest > $gamesWonHome)) {
					$pointsGuest += $round->getProperties()['pointsPerMatchWin'];
					$pointsHome += $round->getProperties()['pointsPerMatchLoss'];
				} elseif (
					(($pointsHome != 0 || $pointsGuest != 0) && $pointsHome == $pointsGuest) ||
					($pointsHome == 0 && $pointsGuest == 0 && $gamesWonHome != 0 && $gamesWonGuest != 0 && $gamesWonHome == $gamesWonGuest)
				){
					$pointsGuest += $round->getProperties()['pointsPerMatchDraw'];
					$pointsHome += $round->getProperties()['pointsPerMatchDraw'];
				}
				
				$roundData[$th->getId()]->setPoints($roundData[$th->getId()]->getPoints() + $pointsHome);
				$roundData[$tg->getId()]->setPoints($roundData[$tg->getId()]->getPoints() + $pointsGuest);
				
				if(!$round->getProperties()['ignoreColors']){
					$roundData[$th->getId()]->setPlayedHome($roundData[$th->getId()]->getPlayedHome() + 1);
					$roundData[$tg->getId()]->setPlayedGuest($roundData[$tg->getId()]->getPlayedGuest() + 1);

					$roundData[$th->getId()]->setPenultimateGameHome($roundData[$th->getId()]->getPreviousGameHome());
					$roundData[$th->getId()]->setPreviousGameHome(true);
					$roundData[$tg->getId()]->setPenultimateGameHome($roundData[$tg->getId()]->getPreviousGameHome());
					$roundData[$tg->getId()]->setPreviousGameHome(false);
				}
			}
			
			foreach($opponents as $id => $opps){
				$roundData[$id]->setBuchholz(0);
				foreach($opps as $op){
					$roundData[$id]->setBuchholz($roundData[$id]->getBuchholz() + $op->getPoints());
				}
			}
			
			$result[$round->getId()] = $roundData;
			$previousRoundData = $roundData;
		}
		
		return $result;
	}
}
