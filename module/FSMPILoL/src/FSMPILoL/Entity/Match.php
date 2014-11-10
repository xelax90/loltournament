<?php
namespace FSMPILoL\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface; 
use Zend\Json\Json;
use JsonSerializable;

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
class Match implements InputFilterAwareInterface, JsonSerializable
{
	protected $inputFilter;
 	
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
	 * @ORM\Column(type="datetime");
	 */
	protected $time;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $foodleURL;
	
	/**
	 * @ORM\OneToMany(targetEntity="Game", mappedBy="match", cascade={"persist", "remove"})
	 */
	protected $games;
	
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

	public function getTime(){
		return $this->time;
	}

	public function setTime($time){
		$this->time = $time;
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
	 * Populate from an array.
	 *
	 * @param array $data
	 */
	public function populate($data = array()){
		if(!empty($data['id']))
			$this->setId($data['id']);
		$this->setNumber($data['number']);
		if(!empty($data['round']))
			$this->setRound($data['round']);
		if(!empty($data['teamHome']))
			$this->setTeamHome($data['teamHome']);
		if(!empty($data['teamGuest']))
			$this->setTeamGuest($data['teamGuest']);
		$this->setIsBlocked($data['isBlocked']);
		$this->setTime($data['time']);
		$this->setFoodleUrl($data['foodleURL']);
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
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'pointsHome',
				'required'   => false,
				'filters' => array(
					array('name' => 'Int'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'pointsGuest',
				'required'   => false,
				'filters' => array(
					array('name' => 'Int'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'anmerkung',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'isBlocked',
				'required'   => true,
				'filters' => array(
					array('name' => 'Boolean'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'foodleURL',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			
			$this->inputFilter = $inputFilter;        
		}

		return $this->inputFilter;
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