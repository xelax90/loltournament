<?php
namespace FSMPILoL\Tournament;

use FSMPILoL\Entity\Tournament;
use FSMPILoL\Entity\Player;
use FSMPILoL\Entity\Team;

use FSMPILoL\Riot\RiotAPI;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Takes all tournament registrations and creates teams with 5 players each
 */
class TeamMatcher{
	
	protected $matched;
	protected $teams;
	protected $singles;
	protected $subs;
	private $fixed_subs = array();
	
	protected $serviceLocator;
	
	protected $api;
	protected $tournament;
	protected $em;
	protected $anmeldung;
	protected $entityManager;

	public function getServiceLocator(){
		return $this->serviceLocator;
	}
	
	public function getEntityManager(){
		if (null === $this->entityManager) {
			$this->entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->entityManager;
	}
	
	public function getAPI(){
		if(null === $this->api){
			$this->api = new RiotAPI($this->getServiceLocator());
		}
		return $this->api;
	}
	
	public function getAnmeldung(){
		if(null === $this->anmeldung){
			$this->anmeldung = new Anmeldung($this->getTournament(), $this->ServiceLocator());
		}
		return $this->anmeldung;
	}
	
	public function __construct(Tournament $tournament, ServiceLocatorInterface $sl){
		$this->tournament = $tournament;
		$this->serviceLocator = $sl;
		$api = $this->getAPI();
		
		$anmeldungen = $tournament->getAnmeldungen();
		$summoners = $api->getSummoners($anmeldungen);
		
		$this->teams = array();
		$teams = $this->getAnmeldung()->getTeams();
		
		$plCount = 0;
		foreach($teams as $teamname => $team){
			$tTeam = new Team();
			$tTeam->setName($teamname);
			$tTeam->setIcon($team[0]->getIcon());
			
			$players = array();
			/** @var Anmeldung $anmeldung */
			foreach($team as $anmeldung){
				$standardname = RiotAPI::getStandardName($anmeldung->getSummonerName());
				$summoner = $summoners[$standardname];
				$players[] = new Player($anmeldung, $tTeam, false, $summoner, $api);
			}
			
			$tTeam->setPlayers(new ArrayCollection($players));
			
			$plCount += count($tTeam->getPlayers());
			
			/*
			if($tTeam->score() >= 24){
				$split = $this->splitTeam($tTeam);
				foreach($split as $t){
					$this->teams[] = $t;
				}
			} else {
				$this->teams[] = $tTeam;
			}
			*/
			
			$this->teams[] = $tTeam;
		}
		
		$this->singles = array();
		$singles = $this->getAnmeldung()->getSingles();
		
		// calculate number of needed subs
		$subs = array();
		$subbed = array();
		foreach($singles as $anmeldung){
			$standardname = RiotAPI::getStandardName($anmeldung->getSummonerName());
			if($anmeldung->getIsSub() != 2 && !in_array($standardname, $this->fixed_subs)){
				$plCount++;
			} else{
				$summoner = $summoners[$standardname];
				$subs[] = new Player($anmeldung, null, false, $summoner, $api);
				$subbed[] = $standardname;
			}
		}
		$subCount = $plCount % 5;
		if($subCount + count($subs) < 4){
			$subCount += 5;
		}
		
		// choose subs
		$keys = array_keys($singles);
		while($subCount > 0){
			$r = mt_rand(0, count($keys)-1);
			$anmeldung = $singles[$keys[$r]];
			
			if($anmeldung->getIsSub() != 1){
				continue;
			}
			
			$standardname = RiotAPI::getStandardName($anmeldung->getSummonerName());
			$summoner = $summoners[$standardname];
			$subs[] = new Player($anmeldung, null, false, $summoner, $api);
			$subbed[] = $standardname;
			$subCount--;
			unset($keys[$r]);
			$keys = array_values($keys);
		}
		$this->subs = $subs;
		
		$this->singles = array();
		// add remaining singles
		foreach($singles as $anmeldung){
			$standardname = RiotAPI::getStandardName($anmeldung->getSummonerName());
			$summoner = $summoners[$standardname];
			if(in_array($standardname, $subbed)){
				continue;
			}
			$this->singles[] = new Player($anmeldung, null, false, $summoner, $api);
		}
	}
	
	public function match(){
		$toMatch = array();
		
		// prepare data
		foreach($this->teams as $team){
			$toMatch[] = $team;
		}
		
		$icons = array_values($this->getAnmeldung()->getAvailableIcons());
		foreach($this->singles as $i => $player){
			$team = new Team();
			$team->setName("Team ".(100+$i));
			$icon = mt_rand(0,count($icons)-1);
			$team->setIcon($icons[$icon]);
			unset($icons[$icon]);
			$icons = array_values($icons);
			$team->setPlayers(new ArrayCollection(array($player)));
			$toMatch[] = $team;
		}
		
		$matched_counted = array();
		foreach($toMatch as $team){
			$matched_counted[count($team->getPlayers())][] = $team;
		}
		
		foreach(array_keys($matched_counted) as $k){
			usort($matched_counted[$k], array("Team", "compare"));
		}
		
		// match
		$matched = $this->match_step($matched_counted);
		
		// write to database
		$em = $this->getEntityManager();
		$i = 0;
		foreach($this->subs as $sub){
			$em->persist($sub);
		}
		
		foreach($matched as $teams){
			foreach($teams as $team){
				$em->persist($team);
			}
		}
		
		$em->flush();
	}
	
	public function match_step($matched){
		$SCOREMAX = 22;
		
		/*
		// match 3 and 2
		if(!empty($matched[3]) && !empty($matched[2])){
			$length = count($matched[3]);
			$j = 0;
			for($i = 0; count($matched[3]) > 1 && count($matched[2]) > 1; $i++){
				$t1 = $matched[3][$i];
				$t2 = $matched[2][count($matched[2]) - 1 - $j];
				
				if($t1->getScore() + $t2->getScore() <= $SCOREMAX){
					unset($matched[3][$i]);
					unset($matched[2][count($matched[2]) - 1 - $j]);
					$j = 0;
					$newTeam = $this->combineTeams($t1, $t2, $t1->getName());
					$matched[5][] = $newTeam;
				}
				$j++;
			}
			// reset keys
			usort($matched[5], array("Team", "compare"));
			$matched[1] = array_values($matched[1]);
			$matched[3] = array_values($matched[3]);
		}
		*/
		
		// first match 3 and 1 
		if(!empty($matched[3])){
			$length = count($matched[3]);
			for($i = 0; $i < $length; $i++){
				$t1 = $matched[3][$i];
				$t2 = $matched[1][count($matched[1]) - 1 - $i];
				
				unset($matched[3][$i]);
				unset($matched[1][count($matched[1]) - 1 - $i]);
				
				$newTeam = $this->combineTeams($t1, $t2, $t1->getName());
				$matched[4][] = $newTeam;
			}
			// reset keys
			usort($matched[4], array("Team", "compare"));
			$matched[1] = array_values($matched[1]);
		}
		
		// then match 1 and 1 
		$last = count($matched[1]) - 1;
		$length = count($matched[1]);
		for($i = 0; count($matched[1]) > 1; $i++){
			$t1 = $matched[1][$i];
			$t2 = $matched[1][$last];
			
			unset($matched[1][$i]);
			unset($matched[1][$last]);
			
			$last--;
			if(substr(strtolower($t1->getName()), 0, 6) == "team 1"){
				$teamname = $t2->getName();
			} else{
				$teamname = $t1->getName();
			}
			$newTeam = $this->combineTeams($t1, $t2, $teamname);
			
			$matched[2][] = $newTeam;
		}
		
		$matched[1] = array_values($matched[1]);
		$matched[2] = array_values($matched[2]);
		
		// split one 2-team if the number is not even
		if(count($matched[2]) % 2 != 0){
			$length = count($matched[2]);
			for($i = 0; $i < $length; $i++){
				$t1 = $matched[2][$i];
				if(substr(strtolower($t1->getName()), 0, 6) == "team 1"){
					$split = $this->fullSplitTeam($matched[2][$i]);
					unset($matched[2][$i]);
					foreach($split as $t){
						$matched[1][] = $t;
					}
					break;
				}
			}
		}
		
		// reset keys
		$matched[1] = array_values($matched[1]);
		usort($matched[2], array("Team", "compare"));
		
		
		// now match 2 and 2
		$last = count($matched[2]) - 1;
		$length = count($matched[2]);
		for($i = 0; count($matched[2]) > 1; $i++){
			$t1 = $matched[2][$i];
			$t2 = $matched[2][$last];
			
			unset($matched[2][$i]);
			unset($matched[2][$last]);
			
			$last--;
			
			if(substr(strtolower($t1->getName()), 0, 6) == "team 1"){
				$teamname = $t2->getName();
			} else{
				$teamname = $t1->getName();
			}
			$newTeam = $this->combineTeams($t1, $t2, $teamname);
			$matched[4][] = $newTeam;
		}
		$matched[2] = array_values($matched[2]);
		usort($matched[4], array("Team", "compare"));
		
		$availablePlayers = count($matched[4])*4 + count($matched[1]);
		$teamedPlayers = $availablePlayers - $availablePlayers % 5;
		$teamCount = $teamedPlayers / 5;
		
		// split 4-teams such that there are enough free players to fit
		$c = 0;
		$length = count($matched[4]);
		for($i = 0; $i < $length; $i++){
			$t = $matched[4][$i];
			if(substr(strtolower($t->getName()), 0, 6) == "team 1" && $length - $c > $teamCount){
				$split = $this->fullSplitTeam($matched[4][$i]);
				unset($matched[4][$i]);
				foreach($split as $t){
					$matched[1][] = $t;
				}
				$c++;
			}
		}

		
		$matched[4] = array_values($matched[4]);
		usort($matched[4], array("Team", "compare"));
		usort($matched[1], array("Team", "compare"));
		
		// match 4-1
		$length = count($matched[4]);
		for($i = 0; $i < $length; $i++){
			if(count($matched[1]) > 0){
				
				$max = max(array_keys($matched[1]));
				$min = min(array_keys($matched[1]));
				for($j = $max; $j >= $min; $j--){
					//echo ".$i-$j-";
					if(!empty($matched[1][$j])){
						if($matched[4][$i]->getScore() + $matched[1][$j]->getScore() <= $SCOREMAX || $j == $min){
							$r = $this->combineTeams($matched[4][$i], $matched[1][$j], $matched[4][$i]->getName());
							unset($matched[4][$i]);
							unset($matched[1][$j]);
							$matched[5][] = $r;
							break;
						}
					}
				}
			}
		}
		
		return $matched;
	}
	
	public function combineTeams($a, $b, $teamname){
		$team = new Team();
		$team->setName($teamname);
		$team->setIcon($a->icon);
		$players = array();
		foreach($a->players as $player){
			$players[] = $player;
			$player->setTeam($team);
		}
		foreach($b->players as $player){
			$players[] = $player;
			$player->setTeam($team);
		}
		$team->setPlayers(new ArrayCollection($players));
		return $team;
	}
	
	public function splitTeam($team){
		$t1 = new Team();
		$t1->setName($team->getName()."_1");
		$t1->setIcon($team->icon);
		
		$t2 = new Team();
		$t2->setName($team->getName()."_2");
		$t2->setIcon($team->getIcon());
		
		$t1Players = array();
		$t2Players = array();
		$i = 0;
		foreach($team->getPlayers() as $player){
			if($i < count($team->getPlayers()) / 2){
				$t1Players[] = $player;
				$player->setTeam($t1);
			} else{
				$t2Players[] = $player;
				$player->setTeam($t2);
			}
			$i++;
		}
		$t1->setPlayers(new ArrayCollection($t1Players));
		$t2->setPlayers(new ArrayCollection($t2Players));
		
		return array($t1, $t2);
	}
	
	public function fullSplitTeam($team){
		$teams = array();
		foreach($team->players as $k => $player){
			$t = new Team();
			$t->setName($team->getName()."_".($k+1));
			$t->setIcon($team->getIcon());
			$t->setPlayers(new ArrayCollection(array($player)));
			$player->setTeam($t);
			$teams[] = $t;
		}
		return $teams;
	}
	
	public function getScoreSum(){
		$score = 0;
		foreach($this->teams as $team){
			$score += $team->getScore();
		}
		
		foreach($this->singles as $player){
			$score += $player->getScore();
		}
		return $score;
	}
	
	public function getAverageScore(){
		$score = $this->getScoreSum();
		
		$count = 0;
		foreach($this->teams as $team){
			$count += count($team->getPlayers());
		}
		
		$count += count($this->singles);
		
		return $score / $count * 5;
	}
}
