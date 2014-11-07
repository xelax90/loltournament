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
 * @ORM\Table(name="player")
 * @property int $id
 * @property Anmeldung $anmeldung
 * @property Team $team
 * @property boolean $isCaptain
 * @property int $summonerId
 */
class Player implements InputFilterAwareInterface, JsonSerializable
{
	protected $inputFilter;
 	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
     * @ORM\OneToOne(targetEntity="Anmeldung", inversedBy="player")
	 * @ORM\JoinColumn(name="anmeldung_id", referencedColumnName="id")
	 */
	protected $anmeldung;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
	 */
	protected $team;
 	
	/**
	 * @ORM\Column(type="boolean");
	 */
	protected $isCaptain;
 	
	/**
	 * @ORM\Column(type="integer");
	 */
	protected $summonerId;
	

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
		$this->summonerId = $summonerId;
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
				'name'       => 'isCaptain',
				'required'   => false,
				'filters' => array(
					array('name' => 'Boolean'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'summonerId',
				'required'   => false,
				'filters' => array(
					array('name' => 'Int'),
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
			"anmeldung" => $this->getAnmeldung(),
			"team" => $this->getTeam(),
			"isCaptain" => $this->getIsCaptain(),
			"summonerId" => $this->getSummonerId(),
		);
		return $data;
	}
}