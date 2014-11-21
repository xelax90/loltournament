<?php
namespace FSMPILoL\Tournament\RoundCreator;
use FSMPILoL\Entity\Round;
use FSMPILoL\Entity\Match;
use FSMPILoL\Entity\Game;
use Doctrine\Common\Collections\ArrayCollection;

class RandomRoundCreator extends AbstractRoundCreator {
	const roundType = 'random';
	
	public function nextRound(AlreadyPlayedInterface $gameCheck, \DateTime $startDate, $properties, $isHidden = true, $duration = 14, $timeForDates = 7){
		$properties = $properties + $this->getDefaultProperties();
		
		$round = new Round();
		$round->setNumber($this->getGroup()->getMaxRoundNumber() + 1);
		$round->setGroup($this->getGroup()->getGroup());
		$round->setType($this->getType());
		$round->setProperties($properties);
		$round->setStartDate($startDate);
		$round->setDuration($duration);
		$round->setTimeForDates($timeForDates);
		$round->setIsHidden($isHidden);
		
		$em = $this->getEntityManager();
		
		$teams = $this->getGroup()->getGroup()->getTeams();
		if(count($teams) % 2 != 0)
			$teams[] = null;
		
		do {
			$roundOK = true;
			shuffle($teams);
			
			for($i = 0; $i+1 < count($teams); $i += 2){
				if($gameCheck->alreadyPlayed($teams[$i], $teams[$i+1])){
					$roundOK = false;
					break;
				}
			}
			
		} while (!$roundOK);
		
		$matches = array();
		$number = 1;
		for($i = 0; $i+1 < count($teams); $i += 2){
			$match = new Match();
			$match->setNumber($number);
			$match->setRound($round);
			$match->setTeamHome($teams[$i]);
			$match->setTeamGuest($teams[$i+1]);
			$this->createGamesForMatch($match);
			$matches[] = $match;
			$number++;
		}
		$round->setMatches(new ArrayCollection($matches));
		$em->persist($round);
		$em->flush();
	}

	protected function _getDefaultProperties() {
		return array(
			'pointsPerGamePoint' => 0,
			'pointsPerMatchWin' => 1,
			'ignoreColors' => true
		);
	}

	public function getType() {
		return self::roundType;
	}

}
