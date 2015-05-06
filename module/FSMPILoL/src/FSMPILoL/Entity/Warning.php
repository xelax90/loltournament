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
 * @ORM\Table(name="warning")
 * @property int $id
 * @property Player $anmeldung
 * @property Team $team
 * @property string $comment
 */
class Warning implements InputFilterAwareInterface, JsonSerializable
{
	protected $inputFilter;
 	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $team;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Player")
	 * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $player;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $comment;
	
	public function getId() {
		return $this->id;
	}

	public function getTeam() {
		return $this->team;
	}

	public function getPlayer() {
		return $this->player;
	}

	public function getComment() {
		return $this->comment;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function setTeam($team) {
		$this->team = $team;
		return $this;
	}

	public function setPlayer($player) {
		$this->player = $player;
		return $this;
	}

	public function setComment($comment) {
		$this->comment = $comment;
		return $this;
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
				'name'       => 'comment',
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
			"anmeldung" => $this->getAnmeldung(),
			"team" => $this->getTeam(),
			"isCaptain" => $this->getIsCaptain(),
			"summonerId" => $this->getSummonerId(),
		);
		return $data;
	}
}