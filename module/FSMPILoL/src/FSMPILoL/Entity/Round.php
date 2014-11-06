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
class Round implements InputFilterAwareInterface, JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="Group")
	 * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
	 */
	protected $group;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $type;
 	
	/**
	 * @ORM\Column(type="boolean");
	 */
	protected $isHidden;
 	
	/**
     * @ORM\OneToMany(targetEntity="Game", mappedBy="round")
	 */
	protected $games;
 	
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
		return $this->team;
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

	public function getGames(){
		return $this->games;
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
		if(!empty($data['group']))
			$this->setGroup($data['group']);
		$this->setType($data['type']);
		$this->setProperties($data['properties']);
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
				'name'       => 'type',
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
			"number" => $this->getNumber(),
			"group_id" => $this->getGroup()->getId(),
			"isHidden" => $this->getIsHidden(),
			"type" => $this->getType(),
			"properties" => $this->getProperties(),
		);
		return $data;
	}
}