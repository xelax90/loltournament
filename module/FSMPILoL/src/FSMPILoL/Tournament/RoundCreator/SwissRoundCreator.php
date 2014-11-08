<?php
namespace FSMPILoL\Tournament\RoundCreator;
use FSMPILoL\Entity\Round;
use FSMPILoL\Entity\Match;
use FSMPILoL\Entity\Game;
use Doctrine\Collection\ArrayCollection;

class SwissRoundCreator extends AbstractRoundCreator {
	
	public function nextRound(AlreadyPlayedInterface $gameCheck, DateTime $startDate, $properties, $isHidden = true, $duration = 14, $timeForDates = 7){
		$defaultProperties = array(
			'pointsPerGamePoint' => 1,
			'pointsPerMatchWin' => 0,
			'pointsPerMatchDraw' => 0,
			'pointsPerMatchLoss' => 0,
			'pointsPerMatchFree' => 2,
			'ignoreColors' => false
		);
		$properties = $properties + $defaultProperties + $this->globalDefaults;
		
		$round = new Round();
		$round->setNumber($group->getMaxRoundNumber() + 1);
		$round->setGroup($this->getGroup());
		$round->setProperties($properties);
		$round->setStartDate($startDate);
		$round->setDuration($duration);
		$round->setTimeForDates($timeForDates);
		$round->setIsHidden($isHidden);
		
		$em = $this->getEntityManager();
		
		$teams = $this->getGroup()->getTeams();
		
		// get round results
		$roundData = $this->getTeamdataPerRound();
		$lastRound = $this->getLastFinishedRound();
		if(!empty($lastRound))
			$roundData = $roundData[$round->getId()];
		else
			$roundData = $roundData[0];
		
		$punktegruppen = array();
		foreach($teams as $team){
			$punktegruppen[$roundData[$team->getId()]->getPoints()][] = $team;
		}
		krsort($punktegruppen);
		
		$punktegruppenKeys = array_keys($punktegruppen);
		$matches = array();
		foreach($punktegruppenKeys as $keyIndex => $gruppeIndex){
			$punktegruppe = $punktegruppen[$gruppeIndex];
			
			//echo "<br> start of loop: <br>";
			//foreach($punktegruppe as $k => $v) echo $k . " " . $v . " - ";
			
			usort($punktegruppe, array("Team", "compareFarberwartung"));
			$matched = array();
			for($i = 0; $i < count($punktegruppe); $i++):
				$t1 = $punktegruppe[$i];
				
				if(!$t1->hasCaptain()) continue;
				if($t1->getIsBlocked()) continue;
				if(in_array($i, $matched)) continue;
				
				$besteVerteilung = array();
				switch($t1->getFarberwartung()):
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
						if($t2->getFarberwartung() != $erwartung && !$round->getProperties()['ignoreColors']) continue;
						if($gameCheck->alreadyPlayed($t1, $t2)) continue;
						
						$teamHome = null;
						$teamGuest = null;
						if($farberwartungen[$t1->getFarberwartung()] < $farberwartungen[$t2->getFarberwartung()]){
							$teamHome = $t2;
							$teamGuest = $t1;
						} else {
							$teamHome = $t1;
							$teamGuest = $t2;
						}
						
						$matched[] = $i;
						$matched[] = $j;
						
						$match = new Match();
						$match->setNumber($number);
						$match->setRound($round);
						$match->setTeamHome($teamHome);
						$match->setTeamGuest($teamGuest);
						$this->createGamesForMatch($match);
						$matches[] = $match;
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
						$match->setNumber($number);
						$match->setRound($round);
						$match->setTeamHome($team);
						$match->setTeamGuest(null);
						$match->setPointsHome($round->getParameter()['pointsPerMatchFree']);
						$match->setPointsGuest(0);
						$matches[] = $match;
						$matched[] = $tnr;
					}
				}
			}
		}
		
		$round->setMatches(new ArrayCollection($matches));
		
		$em->persist($round);
		$em->flush();
	}
}
