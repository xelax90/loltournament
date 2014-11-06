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
 * @ORM\Table(name="game")
 * @property int $id
 * @property Match $match
 * @property int $number
 * @property Team $teamBlue
 * @property Team $teamPurple
 * @property int $pointsBlue
 * @property int $pointsPurple
 * @property boolean $isBlocked
 * @property string $meldungHome
 * @property string $meldungGuest
 * @property string $anmerkungHome
 * @property string $anmerkungGuest
 * @property string $screenHome
 * @property string $screenGuest
 * @property string $tournamentCode
 * @property string $report
 * @property string $streamLink
 */
class Game implements InputFilterAwareInterface, JsonSerializable
{
	protected $inputFilter;
 	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Match")
	 * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
	 */
	protected $match;
 	
	/**
	 * @ORM\Column(type="integer");
	 */
	protected $number;
 	
	/**
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_blue_id", referencedColumnName="id")
	 */
	protected $teamBlue;
 	
	/**
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_purple_id", referencedColumnName="id")
	 */
	protected $teamPurple;
 	
	/**
	 * @ORM\Column(type="integer", nullable=true);
	 */
	protected $pointsBlue;
 	
	/**
	 * @ORM\Column(type="integer", nullable=true);
	 */
	protected $pointsPurple;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $meldungHome;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $meldungGuest;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $anmerkungHome;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $anmerkungGuest;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $screenHome;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $screenGuest;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $report;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $streamLink;
 	

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getMatch(){
		return $this->match;
	}

	public function setMatch($match){
		$this->match = $match;
	}

	public function getNumber(){
		return $this->number;
	}

	public function setNumber($number){
		$this->number = $number;
	}

	public function getTeamBlue(){
		return $this->teamBlue;
	}

	public function setTeamBlue($teamBlue){
		$this->teamBlue = $teamBlue;
	}

	public function getTeamPurple(){
		return $this->teamPurple;
	}

	public function setTeamPurple($teamPurple){
		$this->teamPurple = $teamPurple;
	}

	public function getPointsBlue(){
		return $this->pointsBlue;
	}

	public function setPointsBlue($pointsBlue){
		$this->pointsBlue = $pointsBlue;
	}

	public function getPointsPurple(){
		return $this->pointsPurple;
	}

	public function setPointsPurple($pointsPurple){
		$this->pointsPurple = $pointsPurple;
	}

	public function getMeldungHome(){
		return $this->meldungHome;
	}

	public function setMeldungHome($meldungHome){
		$this->meldungHome = $meldungHome;
	}

	public function getMeldungGuest(){
		return $this->meldungGuest;
	}

	public function setMeldungGuest($meldungGuest){
		$this->meldungGuest = $meldungGuest;
	}

	public function getAnmerkungHome(){
		return $this->anmerkungHome;
	}

	public function setAnmerkungHome($anmerkungHome){
		$this->anmerkungHome = $anmerkungHome;
	}

	public function getAnmerkungGuest(){
		return $this->anmerkungGuest;
	}

	public function setAnmerkungGuest($anmerkungGuest){
		$this->anmerkungGuest = $anmerkungGuest;
	}

	public function getScreenHome(){
		return $this->screenHome;
	}

	public function setScreenHome($screenHome){
		$this->screenHome = $screenHome;
	}

	public function getScreenGuest(){
		return $this->screenGuest;
	}

	public function setScreenGuest($screenGuest){
		$this->screenGuest = $screenGuest;
	}

	public function getReport(){
		return $this->report;
	}

	public function setReport($report){
		$this->report = $report;
	}

	public function getStreamLink(){
		return $this->streamLink;
	}

	public function setStreamLink($streamLink){
		$this->streamLink = $streamLink;
	}
	
	/**
	 * Populate from an array.
	 *
	 * @param array $data
	 */
	public function populate($data = array()){
		if(!empty($data['id']))
			$this->setId($data['id']);
		if(!empty($data['match']))
			$this->setMatch($data['match']);
		$this->setNumber($data['number']);
		if(!empty($data['teamBlue']))
			$this->setTeamBlue($data['teamBlue']);
		if(!empty($data['teamPurple']))
			$this->setTeamPurple($data['teamPurple']);
		$this->setIsBlocked($data['isBlocked']);
		$this->setMeldungHome($data['meldungHome']);
		$this->setMeldungGuest($data['meldungGuest']);
		$this->setAnmerkungHome($data['anmerkungHome']);
		$this->setAnmerkungGuest($data['anmerkungGuest']);
		$this->setScreenHome($data['screenHome']);
		$this->setScreenGuest($data['screenGuest']);
		$this->setTournamentCode($data['tournamentCode']);
		$this->setReport($data['report']);
		$this->setStreamLink($data['streamLink']);
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
				'name'       => 'pointsBlue',
				'required'   => false,
				'filters' => array(
					array('name' => 'Int'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'pointsPurple',
				'required'   => false,
				'filters' => array(
					array('name' => 'Int'),
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
				'name'       => 'meldungHome',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'meldungGuest',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'anmerkungHome',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'anmerkungGuest',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'screenHome',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'screenGuest',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'tournamentCode',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'report',
				'required'   => false,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'streamLink',
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
			"number" => $this->getNumber(),
			"group_id" => $this->getGroup()->getId(),
			"isHidden" => $this->getIsHidden(),
			"type" => $this->getType(),
			"properties" => $this->getProperties(),
		);
		return $data;
	}
}