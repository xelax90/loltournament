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
 * A Anmeldung
 *
 * @ORM\Entity
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
class Anmeldung implements InputFilterAwareInterface, JsonSerializable
{
	protected $inputFilter;
 	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
	 * @ORM\Column(type="string");
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
	 * @ORM\Column(type="string");
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
	
	/**
	 * Populate from an array.
	 *
	 * @param array $data
	 */
	public function populate($data = array()){
		if(!empty($data['id']))
			$this->setId($data['id']);
		$this->setName($data['name']);
		if(!empty($data['teamName']))
			$this->setTeamName($data['teamName']);
		$this->setEmail($data['email']);
		if(!empty($data['facebook']))
			$this->setFacebook($data['facebook']);
		if(!empty($data['otherContact']))
			$this->setOtherContact($data['otherContact']);
		$this->setSummonerName($data['summonerName']);
		$this->setIsSub($data['isSub']);
		if(!empty($data['anmerkung']))
			$this->setAnmerkung($data['anmerkung']);
		if(!empty($data['icon']))
			$this->setIcon($data['icon']);
		if(!empty($data['tournament']))
			$this->setTournament($data['tournament']);
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
				'name'       => 'teamName',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'name',
				'required'   => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'email',
				'required'   => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' => array(
					array( 'name' => 'EmailAddress' ),
				)
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'facebook',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'otherContact',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'summonerName',
				'required'   => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'isSub',
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
				'name'       => 'icon',
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