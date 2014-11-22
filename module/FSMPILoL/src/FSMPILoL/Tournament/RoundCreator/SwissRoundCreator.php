<?php
namespace FSMPILoL\Tournament\RoundCreator;
use FSMPILoL\Entity\Round;
use FSMPILoL\Entity\Match;
use FSMPILoL\Entity\Game;
use FSMPILoL\Entity\Team;
use Doctrine\Common\Collections\ArrayCollection;
use FSMPILoL\Tournament\Teamdata;


class SwissRoundCreator extends AbstractRoundCreator {
	
	const roundType = 'swiss';
	
	public function nextRound(AlreadyPlayedInterface $gameCheck, \DateTime $startDate, $properties, $isHidden = true, $duration = 14, $timeForDates = 7){
		$farberwartungen = array("+g" => -3, "g" => -2, "-o" => -1, "o" => 0, "+o" => 1, "h" => 2, "+h" => 3);
		$this->getGroup()->setAPIData();
		
		$props= $properties + $this->getDefaultProperties();
		
		$round = new Round();
		$round->setNumber($this->getGroup()->getMaxRoundNumber() + 1);
		$round->setGroup($this->getGroup()->getGroup());
		$round->setType($this->getType());
		$round->setProperties($props);
		$round->setStartDate($startDate);
		$round->setDuration($duration);
		$round->setTimeForDates($timeForDates);
		$round->setIsHidden($isHidden);
		
		$em = $this->getEntityManager();
		
		$teams = $this->getGroup()->getGroup()->getTeams();
		
		// get round results
		$roundData = $this->getRoundData();
		
		foreach($teams as $team){
			/* @var $team Team */
			$team->setData($roundData[$team->getId()]);
		}
		
		$punktegruppen = array();
		foreach($teams as $team){
			$punktegruppen[$team->getData()->getPoints()][] = $team;
		}
		krsort($punktegruppen);
		
		$punktegruppenKeys = array_keys($punktegruppen);
		$matches = array();
		$matchCount = 1;
		foreach($punktegruppenKeys as $keyIndex => $gruppeIndex){
			$punktegruppe = $punktegruppen[$gruppeIndex];
			
			//echo "<br> start of loop: <br>";
			//foreach($punktegruppe as $k => $v) echo $k . " " . $v . " - ";
			
			usort($punktegruppe, array("\FSMPILoL\Entity\Team", "compareFarberwartung"));
			$matched = array();
			for($i = 0; $i < count($punktegruppe); $i++):
				$t1 = $punktegruppe[$i];
				
				if(!$t1->hasCaptain()) continue;
				if($t1->getIsBlocked()) continue;
				if(in_array($i, $matched)) continue;
				
				$besteVerteilung = array();
				switch($t1->getData()->getFarberwartung()):
					case "+h" : $besteVerteilung = array("+g", "g", "-o", "+o", "h"); break;
					case "h"  : $besteVerteilung = array("g", "+g", "-o", "+o", "h", "+h"); break;
					case "+o" : $besteVerteilung = array("+g", "g", "-o", "+o", "h", "+h"); break;
					case "-o" : $besteVerteilung = array("+h", "h", "+o", "-o", "g", "+g"); break;
					case "g"  : $besteVerteilung = array("+h", "h", "+o", "-o", "g", "+g"); break;
					case "+g" : $besteVerteilung = array("+h", "h", "+o", "-o", "g"); break;
					default   : $besteVerteilung = array("-o", "+o", "g", "h", "+g", "+h"); break;
				endswitch;
				
				foreach($besteVerteilung as $erwartung){
					if(in_array($i, $matched)) break;
					
					for($j = 0; $j < count($punktegruppe); $j++){
						$t2 = $punktegruppe[$j];
						
						if(in_array($i, $matched)) break;
						if($j == $i) continue;
						if(in_array($j, $matched)) continue;
						if(!$t2->hasCaptain()) continue;
						if($t2->getIsBlocked()) continue;
						if($t2->getData()->getFarberwartung() != $erwartung && !$round->getProperties()['ignoreColors']) continue;
						if($gameCheck->alreadyPlayed($t1, $t2)) continue;
						
						$teamHome = null;
						$teamGuest = null;
						if($farberwartungen[$t1->getData()->getFarberwartung()] < $farberwartungen[$t2->getData()->getFarberwartung()]){
							$teamHome = $t2;
							$teamGuest = $t1;
						} else {
							$teamHome = $t1;
							$teamGuest = $t2;
						}
						
						$matched[] = $i;
						$matched[] = $j;
						
						$match = new Match();
						$match->setNumber($matchCount);
						$match->setRound($round);
						$match->setTeamHome($teamHome);
						$match->setTeamGuest($teamGuest);
						$this->createGamesForMatch($match);
						$matches[] = $match;
						$matchCount++;
						break;
					}
				}
			endfor;
			
			
			// Test, ob alle (bis auf einen) ein Match haben
			if(count($punktegruppe) - count($matched) > 0){
				
				// Suche nächste Punktegruppe
				$nextGroup = null;
				if($keyIndex < count($punktegruppenKeys) - 1)
					$nextGroup = $punktegruppenKeys[$keyIndex + 1];
				
				// Suche nicht gematches Team
				$unmatchedTeams = array();
				foreach($punktegruppe as $tnr => $team){
					if(!in_array($tnr, $matched)){
						$unmatchedTeams[] = array(array_search($team, $punktegruppen[$gruppeIndex]), $team);
					}
				}
				
				
				if($nextGroup !== null){
					// Wenn es noch eine Gruppe gibt, lose Team eine Gruppe runter
					foreach($unmatchedTeams as $unmatched){
						list($tnr, $team) = $unmatched;
						$punktegruppen[$nextGroup][] = $team;
						
						//echo PHP_EOL."<br>current_before: <br>".PHP_EOL;
						//foreach($punktegruppen[$gruppeIndex] as $k => $v) echo $k." ".$v." - ";
						
						unset($punktegruppen[$gruppeIndex][$tnr]);
						
						//var_dump($tnr);
						//var_dump($team->teamname);
						
						//echo PHP_EOL."<br>next: <br>".PHP_EOL;
						//foreach($punktegruppen[$nextGroup] as $k => $v) echo $k." ".$v." - ";
						//echo PHP_EOL."<br>current_after: <br>".PHP_EOL;
						//foreach($punktegruppen[$gruppeIndex] as $k => $v) echo $k." ".$v." - ";
						//unset($punktegruppe[$i]);
					}
				} else {
					// Sonst erzeuge kampfloses Spiel
					foreach($unmatchedTeams as $unmatched){							
						list($tnr, $team) = $unmatched;
						if($team->getIsBlocked()) continue;
						
						$match = new Match();
						$match->setNumber($matchCount);
						$match->setRound($round);
						$match->setTeamHome($team);
						$match->setTeamGuest(null);
						$match->setPointsHome($round->getProperties()['pointsPerMatchFree']);
						$match->setPointsGuest(0);
						$match->setIsBlocked(true);
						$matches[] = $match;
						$matchCount++;
						$matched[] = $tnr;
					}
				}
			}
		}
		
		$round->setMatches(new ArrayCollection($matches));
		
		$em->persist($round);
		$em->flush();
		
		$em->refresh($this->getGroup()->getGroup());
		$this->getGroup()->setTeamdata();
	}
	
	protected function getRoundData(){
		$roundData = $this->getGroup()->getTeamdataPerRound();
		$lastRound = $this->getGroup()->getLastFinishedRound();
		$currentRound = $this->getGroup()->getCurrentRound();
		$result = array();
		if(!empty($lastRound))
			$data = $roundData[$lastRound->getId()];
		else
			$data = $roundData[0];
		foreach($data as $k => $v){
			$result[$k] = new Teamdata($v);
		}
		
		if($lastRound == $currentRound) 
			return $result;
		
		$data = $roundData[$currentRound->getId()];
		foreach($data as $k => $v){
			$result[$k]->setPlayedHome($v->getPlayedHome());
			$result[$k]->setPlayedGuest($v->getPlayedGuest());
			$result[$k]->setPreviousGameHome($v->getPreviousGameHome());
			$result[$k]->setPenultimateGameHome($v->getPenultimateGameHome());
		}
		return $result;
	}
	
	protected function _getDefaultProperties() {
		return array(
			'pointsPerGamePoint' => 1,
			'pointsPerMatchWin' => 0,
			'pointsPerMatchDraw' => 0,
			'pointsPerMatchLoss' => 0,
			'pointsPerMatchFree' => 2,
			'ignoreColors' => false
		);
	}

	public function getType() {
		return self::roundType;
	}

}
