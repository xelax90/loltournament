<?php
namespace FSMPILoL\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A Player
 *
 * @ORM\Entity
 * @ORM\Table(name="matches")
 * @property int $id
 * @property Round $round
 * @property int $number
 * @property Team $teamHome
 * @property Team $teamGuest
 * @property int $pointsHome
 * @property int $pointsHome
 * @property string $anmerkung
 * @property boolean $isBlocked
 * @property DateTime $time
 * @property string $foodleURL
 */
class Match implements JsonSerializable
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
	 * @ORM\Column(type="integer");
	 */
	protected $number;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Round", inversedBy="matches")
	 * @ORM\JoinColumn(name="round_id", referencedColumnName="id")
	 */
	protected $round;
 	
	/**
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_home_id", referencedColumnName="id")
	 */
	protected $teamHome;
 	
	/**
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_guest_id", referencedColumnName="id")
	 */
	protected $teamGuest;
 	
	/**
	 * @ORM\Column(type="integer", nullable=true);
	 */
	protected $pointsHome;
 	
	/**
	 * @ORM\Column(type="integer", nullable=true);
	 */
	protected $pointsGuest;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $anmerkung;
 	
	/**
	 * @ORM\Column(type="boolean");
	 */
	protected $isBlocked = false;
 	
	/**
	 * @ORM\Column(type="datetime", nullable=true);
	 */
	protected $timeHome;
 	
	/**
	 * @ORM\Column(type="datetime", nullable=true);
	 */
	protected $timeGuest;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $foodleURL;
	
	/**
	 * @ORM\OneToMany(targetEntity="Game", mappedBy="match", cascade={"persist", "remove"})
	 */
	protected $games;
	
	public function __construct() {
		$this->games = new ArrayCollection();
	}
	
	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getNumber(){
		return $this->number;
	}

	public function setNumber($number){
		$this->number = $number;
	}

	public function getRound(){
		return $this->round;
	}

	public function setRound($round){
		$this->round = $round;
	}

	public function getTeamHome(){
		return $this->teamHome;
	}

	public function setTeamHome($teamHome){
		$this->teamHome = $teamHome;
	}

	public function getTeamGuest(){
		return $this->teamGuest;
	}

	public function setTeamGuest($teamGuest){
		$this->teamGuest = $teamGuest;
	}

	public function getPointsHome(){
		return $this->pointsHome;
	}

	public function setPointsHome($pointsHome){
		$this->pointsHome = $pointsHome;
	}

	public function getPointsGuest(){
		return $this->pointsGuest;
	}

	public function setPointsGuest($pointsGuest){
		$this->pointsGuest = $pointsGuest;
	}

	public function getAnmerkung(){
		return $this->anmerkung;
	}

	public function setAnmerkung($anmerkung){
		$this->anmerkung = $anmerkung;
	}

	public function getIsBlocked(){
		return $this->isBlocked;
	}

	public function setIsBlocked($isBlocked){
		$this->isBlocked = $isBlocked;
	}

	public function getTimeHome(){
		return $this->timeHome;
	}

	public function setTimeHome($time){
		$this->timeHome = $time;
	}

	public function getTimeGuest(){
		return $this->timeGuest;
	}

	public function setTimeGuest($time){
		$this->timeGuest = $time;
	}

	public function getTime(){
		if(!empty($this->getTimeHome()) && !empty($this->getTimeGuest())){
			return $this->timeHome;
		}
		return null;
	}

	public function setTime($time){
		$this->timeHome = $time;
	}

	public function getFoodleURL(){
		return $this->foodleURL;
	}

	public function setFoodleURL($foodleURL){
		$this->foodleURL = $foodleURL;
	}
	
	public function getGames(){
		return $this->games;
	}
	
	public function setGames($games){
		$this->games = $games;
	}

	/**
	 * Returns json String
	 * @return string
	 */
	public function toJson(){
		$data = $this->jsonSerialize();
		return Json::encode($data, true, array('silenceCyclicalExceptions' => true));
	}
	
	public function getArrayCopy(){
		return $this->jsonSerialize();
	}
	
	/**
	 * Returns data to show in json
	 * @return array
	 */
	public function jsonSerialize(){
		$data = array(
			"id" => $this->getId(),
			"round_id" => $this->getRound()->getId(),
			"number" => $this->getNumber(),
			"teamHome" => $this->getTeamHome(),
			"teamGuest" => $this->getTeamGuest(),
			"pointsHome" => $this->getPointsHome(),
			"pointsGuest" => $this->getPointsGuest(),
			"anmerkung" => $this->getAnmerkung(),
			"isBlocked" => $this->getIsBlocked(),
			"time" => $this->getTime(),
			"foodleURL" => $this->getFoodleURL(),
		);
		return $data;
	}
}