<?php
namespace FSMPILoL\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Team
 *
 * @ORM\Entity(repositoryClass="FSMPILoL\Model\TeamRepository")
 * @ORM\Table(name="team")
 * @property int $id
 * @property string $name
 * @property Group $group
 * @property int $number
 * @property boolean $isBlocked
 * @property string $icon
 * @property LoLUser $ansprechpartner
 */
class Team implements JsonSerializable
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $name;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="teams")
	 * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
	 */
	protected $group;
 	
	/**
	 * @ORM\Column(type="integer");
	 */
	protected $number;
 	
	/**
	 * @ORM\Column(type="boolean");
	 */
	protected $isBlocked;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $icon;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $anmerkung;
 	
	/**
     * @ORM\ManyToOne(targetEntity="LoLUser")
	 * @ORM\JoinColumn(name="ansprechpartner_id", referencedColumnName="user_id")
	 */
	protected $ansprechpartner;
	
	/**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="team", cascade={"persist"})
 	 * @ORM\OrderBy({"isCaptain" = "DESC"})
	 */
	protected $players;
	
	/**
     * @ORM\OneToMany(targetEntity="Warning", mappedBy="team")
	 */
	protected $warnings;
	
	protected $data;

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getGroup(){
		return $this->group;
	}

	public function setGroup($group){
		$this->group = $group;
	}

	public function getNumber(){
		return $this->number;
	}

	public function setNumber($number){
		$this->number = $number;
	}

	public function getIsBlocked(){
		return $this->isBlocked;
	}

	public function setIsBlocked($isBlocked){
		$this->isBlocked = $isBlocked;
	}

	public function getIcon(){
		return $this->icon;
	}

	public function setIcon($icon){
		$this->icon = $icon;
	}
	
	public function getWarnings() {
		return $this->warnings;
	}
	
	public function setWarnings($warnings) {
		$this->warnings = $warnings;
		return $this;
	}
	
	/**
	 * @return LoLUser
	 */
	public function getAnsprechpartner(){
		return $this->ansprechpartner;
	}

	public function setAnsprechpartner($ansprechpartner){
		$this->ansprechpartner = $ansprechpartner;
	}
	
	public function getPlayers(){
		return $this->players;
	}
	
	public function setPlayers($players){
		$this->players = $players;
	}
	
	public function getAnmerkung(){
		return $this->anmerkung;
	}
	
	public function setAnmerkung($anmerkung){
		$this->anmerkung = $anmerkung;
	}
	
	public function getData(){
		return $this->data;
	}
	
	public function setData($data){
		$this->data = $data;
	}
	
	public function getGroupName(){
		return 'Gruppe '.$this->getGroup()->getNumber();
	}
	
	public function getScore(){
		$score = 0;
		if($this->getPlayers()){
			$count = 0;
			foreach($this->getPlayers() as $player){
				$count++;
				$score += $player->getScore();
			}
			if($count > 0){
				$score = $score / $count * 5;
			}
		}
		return $score;
	}
	
	public function hasCaptain(){
		foreach($this->getPlayers() as $player){
			if($player->getIsCaptain())
				return true;
		}
		return false;
	}
	
	public static function compare($a, $b){
		return $a->getScore() - $b->getScore();
	}
	
	public static function comparePoints($a, $b){
		$dataA = $a->getData();
		$dataB = $b->getData();
		if(empty($dataA) || empty($dataB)){
			return self::compare($b, $a);
		}
		if($dataB->getPoints() == $dataA->getPoints() && $dataB->getBuchholz() == $dataA->getBuchholz()){
			return self::compare($b, $a);
		}
		if($dataB->getPoints() == $dataA->getPoints()){
			return $dataB->getBuchholz() - $dataA->getBuchholz();
		}
		return $dataB->getPoints() - $dataA->getPoints();
	}

	public static function compareFarberwartung($a, $b){
		$dataA = $a->getData();
		$dataB = $b->getData();
		if(empty($dataA) || empty($dataB)){
			return self::compare($b, $a);
		}
		
		$erwartungen = array("+g" => 3, "+h" => 3, "g" => 2, "h" => 2, "-o" => 1, "+o" => 1, "o" => 0);
		if($dataB->getPoints() != $dataA->getPoints()){
			return $dataB->getPoints() - $dataA->getPoints();
		}
			
		if($dataB->getBuchholz() != $dataA->getBuchholz()){
			return $dataB->getBuchholz() - $dataA->getBuchholz();
		}
		
		if($dataB->getFarberwartung() != $dataA->getFarberwartung()){
			return $erwartungen[$dataB->getFarberwartung()] - $erwartungen[$dataA->getFarberwartung()];
		}
		
		return self::compare($b, $a);
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
			"name" => $this->getName(),
			"group" => $this->getGroup(),
			"number" => $this->getNumber(),
			"isBlocked" => $this->getIsBlocked(),
			"icon" => $this->getIcon(),
			"ansprechpartner" => $this->getAnsprechpartner(),
		);
		return $data;
	}
}