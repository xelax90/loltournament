<?php
namespace FSMPILoL\Entity;

use FSMPILoL\Tournament\RoundCreator\AlreadyPlayedInterface;
use FSMPILoL\Tournament\Teamdata;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface; 
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Group
 *
 * @ORM\Entity
 * @ORM\Table(name="groups")
 * @property int $id
 * @property Tournament $tournament
 * @property int $number
 * @property array $teams
 * @property array $rounds
 */
class Group implements InputFilterAwareInterface, JsonSerializable, AlreadyPlayedInterface
{
	protected $inputFilter;
 	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="groups")
	 * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
	 */
	protected $tournament;
 	
	/**
	 * @ORM\Column(type="integer");
	 */
	protected $number;
 	
	/**
     * @ORM\OneToMany(targetEntity="Team", mappedBy="group")
	 */
	protected $teams;
 	
	/**
	 * @ORM\OneToMany(targetEntity="Round", mappedBy="group")
	 */
	protected $rounds;
	
	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getTournament(){
		return $this->tournament;
	}

	public function setTournament($tournament){
		$this->tournament = $tournament;
	}

	public function getNumber(){
		return $this->number;
	}

	public function setNumber($number){
		$this->number = $number;
	}

	public function getTeams(){
		return $this->teams;
	}

	public function getRounds(){
		return $this->rounds;
	}
	
	public function getMaxRoundNumber(){
		$max = 0;
		foreach($this->getRounds() as $round){
			$max = max($round->getNumber(), $max);
		}
		return $max;
	}
	
	public function setTeamdata(){
		$data = $this->getTeamdataPerRound();
		$round = $this->getCurrentRound();
		$id = 0;
		if(!empty($round)){
			$id = $round->getId();
		}
		foreach($data[$id] as $teamdata){
			$teamdata->getTeam()->setData($teamdata);
		}
	}
	
	public function getLastFinishedRound(){
		$rounds = $this->getRounds()->toArray();
		// sort rounds by round number in descending order
		usort($rounds, function($r1, $r2){return $r2->getNumber() - $r1->getNumber();});
		
		$new = new DateTime();
		foreach($rounds as $round){
			$date = new DateTime($round->getStartDate());
			$date->modify('+'.$round->getDuration().' days');
			
			if($now <= $date)
				return $round;
		}
		return null;
	}
	
	public function getCurrentRound(){
		$rounds = $this->getRounds()->toArray();
		if(empty($rounds))
			return null;
		
		// sort rounds by round number in descending order
		usort($rounds, function($r1, $r2){return $r2->getNumber() - $r1->getNumber();});
		
		$round = null;
		foreach($rounds as $r){
			if($r->getIsHidden())
				continue;
			$round = $r;
			break;
		}
		
		return $round;
	}
	
	public function getTeamdataPerRound(){
		$rounds = $this->getRounds()->toArray();
		$teams = $this->getTeams();
		
		// sort rounds by round number
		usort($rounds, function($r1, $r2){return $r1->getNumber() - $r2->getNumber();});
		
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
			foreach($teams as $team){
				$olddata = $previousRoundData[$team->getId()];
				$roundData[$team->getId()] = new Teamdata($olddata);
			}
			
			// points, buchholz, playedHome, playedGuest, previousGameHome, penultimateGameHome
			foreach($round->getMatches() as $match){
				$th = $match->getTeamHome();
				$tg = $match->getTeamGuest();
				
				if($th && $tg){
					$opponents[$th->getId()][] = $tg;
					$opponents[$tg->getId()][] = $th;
				}
								
				$pointsHome = 0;
				$pointsGuest = 0;
				$gamesWonHome = 0;
				$gamesWonGuest = 0;
				foreach($match->getGames() as $game){
					if($game->getTeamBlue() == $th){
						$pointsHome += $game->getPointsBlue() * $round->getProperties()['pointsPerGamePoint'];
						if($game->getPointsBlue() > $game->getPointsPurple())
							$gamesWonHome++;
					} elseif($game->getTeamPurple() == $th){
						$pointsHome += $game->getPointsPurple() * $round->getProperties()['pointsPerGamePoint'];
						if($game->getPointsPurple() > $game->getPointsBlue())
							$gamesWonHome++;
					}
					
					if($game->getTeamBlue() == $tg){
						$pointsGuest += $game->getPointsBlue() * $round->getProperties()['pointsPerGamePoint'];
						if($game->getPointsBlue() > $game->getPointsPurple())
							$gamesWonGuest++;
					} elseif($game->getTeamPurple() == $tg){
						$pointsGuest += $game->getPointsPurple() * $round->getProperties()['pointsPerGamePoint'];
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
				
				if($th)
					$roundData[$th->getId()]->setPoints($roundData[$th->getId()]->getPoints() + $pointsHome);
				if($tg)
					$roundData[$tg->getId()]->setPoints($roundData[$tg->getId()]->getPoints() + $pointsGuest);
				
				if(!$round->getProperties()['ignoreColors']){
					if($th){
						$roundData[$th->getId()]->setPlayedHome($roundData[$th->getId()]->getPlayedHome() + 1);
						$roundData[$th->getId()]->setPenultimateGameHome($roundData[$th->getId()]->getPreviousGameHome());
						$roundData[$th->getId()]->setPreviousGameHome(true);
					}
					
					if($tg){
						$roundData[$tg->getId()]->setPlayedGuest($roundData[$tg->getId()]->getPlayedGuest() + 1);
						$roundData[$tg->getId()]->setPenultimateGameHome($roundData[$tg->getId()]->getPreviousGameHome());
						$roundData[$tg->getId()]->setPreviousGameHome(false);
					}
				}
			}
			
			foreach($opponents as $id => $opps){
				$roundData[$id]->setBuchholz(0);
				foreach($opps as $op){
					$roundData[$id]->setBuchholz($roundData[$id]->getBuchholz() + $roundData[$op->getId()]->getPoints());
				}
			}
			
			$result[$round->getId()] = $roundData;
			$previousRoundData = $roundData;
		}
		
		return $result;
	}
	
	
	/**
	 * Populate from an array.
	 *
	 * @param array $data
	 */
	public function populate($data = array()){
		if(!empty($data['id']))
			$this->setId($data['id']);
		$this->setNumber($data['number']);
		if(!empty($data['tournament']))
			$this->setTournament($data['tournament']);
	}
 
	public function setInputFilter(InputFilterInterface $inputFilter){
		throw new \Exception("Not used");
	}
 
	/**
	 * Returns input filters for this entity
	 * @return \Zend\InputFilter\InputFilter
	 */
	public function getInputFilter(){
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
 
			$factory = new InputFactory();
 
			$inputFilter->add($factory->createInput(array(
				'name'       => 'id',
				'required'   => true,
				'filters' => array(
					array('name'    => 'Int'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'number',
				'required'   => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			)));
			
			$this->inputFilter = $inputFilter;        
		}

		return $this->inputFilter;
	}
	
	public function alreadyPlayed(Team $t1, Team $t2){
		foreach($this->getRounds() as $round){
			if($round->alreadyPlayed($t1, $t2))
				return true;
		}
		return false;
	}
	
	/**
	 * Returns json String
	 * @return string
	 */
	public function toJson(){
		$data = $this->jsonSerialize();
		return Json::encode($data, true, array('silenceCyclicalExceptions' => true));
	}
	
	/**
	 * Returns data to show in json
	 * @return array
	 */
	public function jsonSerialize(){
		$data = array(
			"id" => $this->getId(),
			"tournament" => $this->getTournament(),
			"number" => $this->getNumber(),
			"teams" => $this->getTeams(),
			"rounds" => $this->getRounds(),
		);
		return $data;
	}
}