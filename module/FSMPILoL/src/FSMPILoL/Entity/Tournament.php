<?php
namespace FSMPILoL\Entity;

use FSMPILoL\Tournament\RoundCreator\AlreadyPlayedInterface;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Tournament
 *
 * @ORM\Entity
 * @ORM\Table(name="tournament")
 * @property int $id
 * @property int $name
 */
class Tournament implements JsonSerializable, AlreadyPlayedInterface
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
	 * @ORM\OneToMany(targetEntity="Anmeldung", mappedBy="tournament");
	 */
	protected $anmeldungen;
	
	/**
	 * @ORM\OneToMany(targetEntity="Group", mappedBy="tournament")
	 */
	protected $groups;
	
	protected $subs;
	
	/**
	 * Getter for ID
	 * @return int
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Getter for Name
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}
	
	public function getAnmeldungen(){
		return $this->anmeldungen;
	}
	
	public function getGroups(){
		return $this->groups;
	}
	
	/** 
	 * Setter for ID
	 * @param int $id
	 */
	public function setId($id){
		$this->id = $id;
	}
	
	/** 
	 * Setter for Name
	 * @param string $name
	 */
	public function setName($name){
		$this->name = $name;
	}
	
	public function getSubs(){
		if(null === $this->subs){
			$this->subs = array();
			foreach($this->getAnmeldungen() as $anmeldung){
				if($player = $anmeldung->getPlayer()){
					if(!$player->getTeam()){
						$this->subs[] = $player;
					}
				}
			}
			usort($this->subs, function($a, $b){return $b->getScore(true) - $a->getScore(true);});
		}
		return $this->subs;
	}
	
	public function alreadyPlayed(Team $t1, Team $t2){
		foreach($this->getGroups() as $group){
			if($group->alreadyPlayed($t1, $t2))
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
			"name" => $this->getName()
		);
		return $data;
	}
}