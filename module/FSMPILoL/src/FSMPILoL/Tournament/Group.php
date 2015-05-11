<?php
namespace FSMPILoL\Tournament;

use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use FSMPILoL\Entity\Group AS GroupEntity;
use DateTime;
use FSMPILoL\Riot\RiotAPI;
use FSMPILoL\Tournament\Summonerdata;

class Group {

	/**
	 * @var EntityManager
	 */
	protected $em;
	
	/**
	 * @var GroupEntity
	 */
	protected $group;
	
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator;
	
	protected $cache;
	
	/** @var array */
	protected $summoners;
	
	/** @var RiotAPI */
	protected $api;
	
	protected function getServiceLocator(){
		return $this->serviceLocator;
	}
	
	public function getEntityManager(){
		if (null === $this->entityManager) {
			$this->entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->entityManager;
	}

	protected function getCache(){
		if (null === $this->cache) {
			$this->cache = $this->getServiceLocator()->get('FSMPILoL\TeamdataCache');
		}
		return $this->cache;
	}
	
	public function getGroup(){
		return $this->group;
	}
	
	public function getTournament(){
		return $this->getGroup()->getTournament();
	}

	public function getAPI(){
		if(null === $this->api){
			$this->api = new RiotAPI($this->getServiceLocator());
		}
		return $this->api;
	}
	
	public function __construct(GroupEntity $group, ServiceLocatorInterface $sl){
		$this->group = $group;
		$this->serviceLocator = $sl;
	}
	
	public function getMaxRoundNumber(){
		$max = 0;
		foreach($this->getGroup()->getRounds() as $round){
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
		$rounds = $this->getGroup()->getRounds()->toArray();
		// sort rounds by round number in descending order
		usort($rounds, function($r1, $r2){return $r2->getNumber() - $r1->getNumber();});
		
		$now = new DateTime();
		foreach($rounds as $round){
			$date = clone $round->getStartDate();
			$date->modify('+'.$round->getDuration().' days');
			
			if ($now <= $date) {
				return $round;
			}
		}
		return null;
	}
	
	public function getCurrentRound(){
		$rounds = $this->getGroup()->getRounds()->toArray();
		if (empty($rounds)) {
			return null;
		}

		// sort rounds by round number in descending order
		usort($rounds, function($r1, $r2){return $r2->getNumber() - $r1->getNumber();});
		
		$round = null;
		foreach($rounds as $r){
			if ($r->getIsHidden()) {
				continue;
			}
			$round = $r;
			break;
		}
		
		return $round;
	}
	
	// TODO optimize cache key to update when data changed
	public function getCacheKey(){
		$rounds = $this->getGroup()->getRounds();
		$maxRoundId = 0;
		foreach($rounds as $round){
			$maxRoundId = max($maxRoundId, $round->getId());
		}
		$teams = $this->getGroup()->getTeams();
		$maxTeamId = 0;
		foreach($teams as $team){
			$maxTeamId = max($maxTeamId, $team->getId());
		}
		return $maxRoundId."_".$maxTeamId;
	}
	
	public function getTeamdataPerRound(){
		$cache = $this->getCache();
		
		$cacheKey = $this->getCacheKey();
		if($cache->hasItem($cacheKey) && !$cache->itemHasExpired($cacheKey) || true){
			$teamdata = unserialize($cache->getItem($cacheKey));
			$teams = $this->getGroup()->getTeams();
			foreach($teamdata as $r => $data){
				foreach($teams as $team){
					$teamdata[$r][$team->getId()]->setTeam($team);
				}
			}
			return $teamdata;
		}
		
		$rounds = $this->getGroup()->getRounds()->toArray();
		$teams = $this->getGroup()->getTeams();
		
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
						if ($game->getPointsBlue() > $game->getPointsPurple()) {
							$gamesWonHome++;
						}
					} elseif($game->getTeamPurple() == $th){
						$pointsHome += $game->getPointsPurple() * $round->getProperties()['pointsPerGamePoint'];
						if ($game->getPointsPurple() > $game->getPointsBlue()) {
							$gamesWonHome++;
						}
					}
					
					if($game->getTeamBlue() == $tg){
						$pointsGuest += $game->getPointsBlue() * $round->getProperties()['pointsPerGamePoint'];
						if ($game->getPointsBlue() > $game->getPointsPurple()) {
							$gamesWonGuest++;
						}
					} elseif($game->getTeamPurple() == $tg){
						$pointsGuest += $game->getPointsPurple() * $round->getProperties()['pointsPerGamePoint'];
						if ($game->getPointsPurple() > $game->getPointsBlue()) {
							$gamesWonGuest++;
						}
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
				
				if ($th) {
					$roundData[$th->getId()]->setPoints($roundData[$th->getId()]->getPoints() + $pointsHome);
				}
				if ($tg) {
					$roundData[$tg->getId()]->setPoints($roundData[$tg->getId()]->getPoints() + $pointsGuest);
				}

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
		
		$cache->addItem($cacheKey, serialize($result));
		return $result;
	}
	
	/**
	 * @return array
	 */
	public function getTournamentSummoners(){
		if(null === $this->summoners){
			$tournament = $this->getTournament();
			if(!$tournament){
				return null;
			}
			$anmeldungen = $tournament->getAnmeldungen();
			$api = $this->getAPI();
			$this->summoners = $api->getSummoners($anmeldungen);
		}
		return $this->summoners;
	}
	
	protected function getSummonerCacheKey(){
		$anmeldungen = $this->getTournament()->getAnmeldungen();
		$maxAnmeldungId = 0;
		foreach($anmeldungen as $anmeldung){
			$maxAnmeldungId = max($maxAnmeldungId, $anmeldung->getId());
		}
		return $maxAnmeldungId;
	}
	
	
	public function setAPIData(){
		$tournament = $this->getTournament();
		$api = $this->getAPI();
		$anmeldungen = $tournament->getAnmeldungen();
		
		$summonerdata = array();
		
		$cache = $this->getServiceLocator()->get('FSMPILoL\SummonerdataCache');
		$cacheKey = $this->getSummonerCacheKey();
		if($cache->hasItem($cacheKey) && !$cache->itemHasExpired($cacheKey) || true){
			$summonerdata = unserialize($cache->getItem($cacheKey));
		} else {
			$summoners = $this->getTournamentSummoners();
			foreach($anmeldungen as $anmeldung){
				$standardname = RiotAPI::getStandardName($anmeldung->getSummonerName());
				if(!empty($summoners[$standardname])){
					$summoner = $summoners[$standardname];
				} else {
					// TODO try to get summoner directly and detect name changes
				}
				$summonerdata[$anmeldung->getId()] = new Summonerdata($api, $anmeldung, $summoner);
			}
			$cache->addItem($cacheKey, serialize($summonerdata));
		}
		
		foreach($anmeldungen as $anmeldung){
			$summonerdata[$anmeldung->getId()]->setAnmeldung($anmeldung);
			$anmeldung->setSummonerdata($summonerdata[$anmeldung->getId()]);
		}
	}
	
}
