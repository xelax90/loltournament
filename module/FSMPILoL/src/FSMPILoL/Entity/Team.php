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
 * A Team
 *
 * @ORM\Entity
 * @ORM\Table(name="team")
 * @property int $id
 * @property string $name
 * @property Group $group
 * @property int $number
 * @property boolean $isBlocked
 * @property string $icon
 * @property User $ansprechpartner
 */
class Team implements InputFilterAwareInterface, JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="Group")
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
     * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="ansprechpartner_id", referencedColumnName="user_id")
	 */
	protected $ansprechpartner;
	

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

	public function getAnsprechpartner(){
		return $this->ansprechpartner;
	}

	public function setAnsprechpartner($ansprechpartner){
		$this->ansprechpartner = $ansprechpartner;
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
		if(!empty($data['group']))
			$this->setGroup($data['group']);
		$this->setNumber($data['number']);
		$this->setIsBlocked($data['isBlocked']);
		$this->setIcon($data['icon']);
		if(!empty($data['ansprechpartner']))
			$this->setAnsprechpartner($data['ansprechpartner']);
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
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'number',
				'required'   => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'isBlocked',
				'required'   => false,
				'filters' => array(
					array('name' => 'Boolean'),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'       => 'icon',
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