<?php
namespace FSMPILoL\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Anmeldung
 *
 * @ORM\Entity(repositoryClass="FSMPILoL\Model\AnmeldungRepository")
 * @ORM\Table(name="anmeldung")
 * @property int $id
 * @property string $teamName
 * @property string $name
 * @property string $email
 * @property string $facebook
 * @property string $otherContact
 * @property string $summonerName
 * @property int $isSub
 * @property string $anmerkung
 * @property string $icon
 * @property Tournament $tournament
 */
class Anmeldung implements JsonSerializable
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
	 * @ORM\Column(type="string", nullable=true);
	 */
	protected $teamName;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $name;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $email;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $facebook;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $otherContact;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $summonerName;
 	
	/**
	 * @ORM\Column(type="integer");
	 */
	protected $isSub;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $anmerkung;
 	
	/**
	 * @ORM\Column(type="string", nullable=true);
	 */
	protected $icon;
	
	/**
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="anmeldungen")
	 * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
	 */
	protected $tournament;

	/**
     * @ORM\OneToOne(targetEntity="Player", mappedBy="anmeldung")
	 */
	protected $player;
	
	/**
	 *
	 * @var \FSMPILoL\Tournament\Summonerdata
	 */
	protected $summonerdata;
	
	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getTeamName(){
		return $this->teamName;
	}

	public function setTeamName($teamName){
		$this->teamName = $teamName;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getEmail(){
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
	}

	public function getFacebook(){
		return $this->facebook;
	}

	public function setFacebook($facebook){
		$this->facebook = $facebook;
	}

	public function getOtherContact(){
		return $this->otherContact;
	}

	public function setOtherContact($otherContact){
		$this->otherContact = $otherContact;
	}

	public function getSummonerName(){
		return $this->summonerName;
	}

	public function setSummonerName($summonerName){
		$this->summonerName = $summonerName;
	}

	public function getIsSub(){
		return $this->isSub;
	}

	public function setIsSub($isSub){
		$this->isSub = $isSub;
	}

	public function getAnmerkung(){
		return $this->anmerkung;
	}

	public function setAnmerkung($anmerkung){
		$this->anmerkung = $anmerkung;
	}

	public function getIcon(){
		return $this->icon;
	}

	public function setIcon($icon){
		$this->icon = $icon;
	}

	public function getTournament(){
		return $this->tournament;
	}

	public function setTournament($tournament){
		$this->tournament = $tournament;
	}
	
	public function getPlayer(){
		return $this->player;
	}

	public function getSummonerdata(){
		return $this->summonerdata;
	}

	public function setSummonerdata($summonerdata){
		return $this->summonerdata = $summonerdata;
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
			"teamName" => $this->getTeamName(),
			"name" => $this->getName(),
			"email" => $this->getEmail(),
			"facebook" => $this->getFacebook(),
			"otherContact" => $this->getOtherContact(),
			"summonerName" => $this->getSummonerName(),
			"isSub" => $this->getIsSub(),
			"anmerkung" => $this->getAnmerkung(),
			"icon" => $this->getIcon(),
			"tournament" => $this->getTournament()
		);
		return $data;
	}
}