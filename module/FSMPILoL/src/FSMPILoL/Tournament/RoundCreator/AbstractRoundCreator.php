<?php
namespace FSMPILoL\Tournament\RoundCreator;

use Doctrine\Common\Collections\ArrayCollection;
use FSMPILoL\Tournament\Group;
use Zend\ServiceManager\ServiceLocatorInterface;
use FSMPILoL\Entity\Game;
use FSMPILoL\Options\RoundCreatorOptions;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Doctrine\ORM\EntityManager;

abstract class AbstractRoundCreator implements ServiceLocatorAwareInterface{
	use ServiceLocatorAwareTrait;
	
	/** @var Group */
	protected $group;
	/** @var EntityManager */
	protected $entityManager;
	
	protected static $globalDefaults = array(
		'gamesPerMatch' => 3,
		'pointsPerGamePoint' => 1,
		'pointsPerMatchWin' => 0,
		'pointsPerMatchDraw' => 0,
		'pointsPerMatchLoss' => 0,
		'pointsPerMatchFree' => 2,
		'ignoreColors' => false
	);
	
	/**
	 * @return EntityManager
	 */
	public function getEntityManager(){
		if (null === $this->entityManager) {
			$this->entityManager = $this->getServiceLocator()->get(EntityManager::class);
		}
		return $this->entityManager;
	}
	
	
	/**
	 * 
	 * @param Group $group
	 * @param string $type
	 * @param ServiceLocatorInterface $sl
	 * @return \FSMPILoL\Tournament\RoundCreator\AbstractRoundCreator
	 */
	public static function getInstance(Group $group, $type, ServiceLocatorInterface $sl){
		/* @var $options RoundCreatorOptions */
		$options = $sl->get(RoundCreatorOptions::class);
		$types = $options->getRoundTypes();
		if(!empty($types[$type]) && class_exists($types[$type]) ){
			try{
				$creator = new $types[$type]();
				$creator->setGroup($group);
				$creator->setServiceLocator($sl);
				return $creator;
			} catch (Exception $ex) {}
		}
		return null;
	}
	
	public static function getGlobalDefaults(){
		return self::$globalDefaults;
	}
	
	public function getTournament(){
		return $this->getGroup()->getTournament();
	}
	
	/**
	 * @return Group
	 */
	public function getGroup(){
		return $this->group;
	}
	
	public function setGroup($group){
		return $this->group = $group;
	}
	
	abstract protected function _getDefaultProperties();
	
	public function getDefaultProperties(){
		return $this->_getDefaultProperties() + self::$globalDefaults;
	}
	
	abstract public function nextRound(AlreadyPlayedInterface $gameCheck, \DateTime $startDate, $properties, $isHidden = true, $duration = 14, $timeForDates = 7);
	
	/**
	 * @return string
	 */
	abstract public function getType();
	
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
