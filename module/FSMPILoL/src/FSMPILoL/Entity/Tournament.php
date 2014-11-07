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
 * A Tournament
 *
 * @ORM\Entity
 * @ORM\Table(name="tournament")
 * @property int $id
 * @property int $name
 */
class Tournament implements InputFilterAwareInterface, JsonSerializable
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
	protected $name;
	
	/**
	 * @ORM\OneToMany(targetEntity="Anmeldung", mappedBy="tournament");
	 */
	protected $anmeldungen;
	
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
	public function setName($id){
		$this->name = $name;
	}
	
	public function getSubs(){
		if(null !== $this->subs){
			$this->subs = array();
			foreach($this->getAnmeldungen() as $anmeldung){
				if($player = $anmeldung->getPlayer()){
					if(!$player->getTeam()){
						$this->subs[] = $player;
					}
				}
			}
		}
		return $this->subs;
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
				'name'       => 'name',
				'required'   => true,
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
			"name" => $this->getName()
		);
		return $data;
	}
}