<?php
namespace FSMPILoL\Entity;

use FSMPILoL\Tournament\RoundCreator\AlreadyPlayedInterface;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Round
 *
 * @ORM\Entity
 * @ORM\Table(name="round")
 * @property int $id
 * @property int $number
 * @property Group $group
 * @property boolean $isHidden
 * @property string $type
 * @property array $properties
 */
class Round implements JsonSerializable, AlreadyPlayedInterface
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
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="groups")
	 * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
	 */
	protected $group;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $type;
	
	/**
	 * @ORM\Column(type="date");
	 */
	protected $startDate;
	
	/**
	 * Duration of the round in days
	 * @ORM\Column(type="integer");
	 */
	protected $duration;
	
	/**
	 * The time, the teams have to add a date for matches in days.
	 * @ORM\Column(type="integer");
	 */
	protected $timeForDates;
 	
	/**
	 * @ORM\Column(type="boolean");
	 */
	protected $isHidden;
 	
	/**
     * @ORM\OneToMany(targetEntity="Match", mappedBy="round", cascade={"persist", "remove"})
	 */
	protected $matches;
 	
	/**
	 * @ORM\Column(type="json_array");
	 */
	protected $properties;
	

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

	public function getGroup(){
		return $this->group;
	}

	public function setGroup($group){
		$this->group = $group;
	}

	public function getIsHidden(){
		return $this->isHidden;
	}

	public function setIsHidden($isHidden){
		$this->isHidden = $isHidden;
	}

	public function getStartDate(){
		return $this->startDate;
	}

	public function setStartDate($startDate){
		$this->startDate = $startDate;
	}

	public function getDuration(){
		return $this->duration;
	}

	public function setDuration($duration){
		$this->duration = $duration;
	}

	public function getTimeForDates(){
		return $this->timeForDates;
	}

	public function setTimeForDates($timeForDates){
		$this->timeForDates = $timeForDates;
	}

	public function getType(){
		return $this->type;
	}

	public function setType($type){
		$this->type = $type;
	}

	public function getProperties(){
		return $this->properties;
	}

	public function setProperties($properties){
		$this->properties = $properties;
	}

	public function getMatches(){
		return $this->matches;
	}

	public function setMatches(\Doctrine\Common\Collections\Collection $matches){
		return $this->matches = $matches;
	}

	public function alreadyPlayed(Team $t1, Team $t2){
		foreach($this->getMatches() as $match){
			if($match->getTeamHome() == $t1 && $match->getTeamGuest() == $t2)
				return true;
			if($match->getTeamHome() == $t2 && $match->getTeamGuest() == $t1)
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
			"number" => $this->getNumber(),
			"group_id" => $this->getGroup()->getId(),
			"isHidden" => $this->getIsHidden(),
			"type" => $this->getType(),
			"properties" => $this->getProperties(),
		);
		return $data;
	}
}