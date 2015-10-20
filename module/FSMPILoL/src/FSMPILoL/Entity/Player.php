<?php
namespace FSMPILoL\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;
use SkelletonApplication\Entity\User;

/**
 * A Player
 *
 * @ORM\Entity(repositoryClass="FSMPILoL\Model\PlayerRepository")
 * @ORM\Table(name="player")
 * @property int $id
 * @property Anmeldung $anmeldung
 * @property Team $team
 * @property boolean $isCaptain
 * @property int $summonerId
 */
class Player implements JsonSerializable
{
	protected $inputFilter;
 	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
     * @ORM\OneToOne(targetEntity="Anmeldung", inversedBy="player", cascade={"persist"})
	 * @ORM\JoinColumn(name="anmeldung_id", referencedColumnName="id")
	 */
	protected $anmeldung;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $team;
 	
	/**
	 * @ORM\Column(type="boolean");
	 */
	protected $isCaptain;
 	
	/**
	 * @ORM\Column(type="integer", nullable=true);
	 */
	protected $summonerId;
	
	/**
	 * @ORM\ManyToOne(targetEntity="SkelletonApplication\Entity\User",inversedBy="players")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="SET NULL")
	 */
	protected $user;
	
	/**
     * @ORM\OneToMany(targetEntity="Warning", mappedBy="player")
	 */
	protected $warnings;
	
	// Data loaded by api
	protected $level;
	protected $tier;
	protected $normalWins;
	protected $rankedWins;
	protected $profileIconId;
	
	protected $score;
	
	public function __construct($anmeldung = null, $team = null, $isCaptain = null){
		if($anmeldung !== null)
			$this->anmeldung = $anmeldung;
		
		if($team !== null)
			$this->team = $team;
		
		if($isCaptain !== null)
			$this->isCaptain = $isCaptain;
	}
	
	
	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getTeam(){
		return $this->team;
	}

	public function setTeam($team){
		$this->team = $team;
	}
	
	/**
	 * @return Anmeldung
	 */
	public function getAnmeldung(){
		return $this->anmeldung;
	}

	public function setAnmeldung($anmeldung){
		$this->anmeldung = $anmeldung;
	}

	public function getIsCaptain(){
		return $this->isCaptain;
	}

	public function setIsCaptain($isCaptain){
		$this->isCaptain = $isCaptain;
	}

	public function getSummonerId(){
		return $this->summonerId;
	}

	public function setSummonerId($summonerId){
		return $this->summonerId = $summonerId;
	}
	
	/**
	 * @return User
	 */
	public function getUser(){
		return $this->user;
	}
	
	public function setUser($user){
		return $this->user = $user;
	}
	
	public function getScore($refresh = false){
		return $this->getAnmeldung()->getSummonerdata()->getScore($refresh);
	}
	
	public function getWarnings() {
		return $this->warnings;
	}

	public function setWarnings($warnings) {
		$this->warnings = $warnings;
		return $this;
	}

	/**
	 * Populate from an array.
	 *
	 * @param array $data
	 */
	public function populate($data = array()){
		if(!empty($data['id']))
			$this->setId($data['id']);
		if(!empty($data['team']))
			$this->setTeam($data['team']);
		if(!empty($data['anmeldung']))
			$this->setAnmeldung($data['anmeldung']);
		$this->setIsCaptain($data['isCaptain']);
		if(!empty($data['summonerId']))
			$this->setSummonerId($data['summonerId']);
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
			"anmeldung" => $this->getAnmeldung(),
			"team" => $this->getTeam(),
			"isCaptain" => $this->getIsCaptain(),
			"summonerId" => $this->getSummonerId(),
		);
		return $data;
	}
}