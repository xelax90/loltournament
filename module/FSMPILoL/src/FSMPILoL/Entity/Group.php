<?php
namespace FSMPILoL\Entity;

use FSMPILoL\Tournament\RoundCreator\AlreadyPlayedInterface;

use Doctrine\ORM\Mapping as ORM;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface; 
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Group
 *
 * @ORM\Entity
 * @ORM\Table(name="group")
 * @property int $id
 * @property Tournament $tournament
 * @property int $number
 * @property array $teams
 * @property array $rounds
 */
class Group implements InputFilterAwareInterface, JsonSerializable, AlreadyPlayedInterface
{
	protected $inputFilter;
 	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Tournament")
	 * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
	 */
	protected $tournament;
 	
	/**
	 * @ORM\Column(type="integer");
	 */
	protected $number;
 	
	/**
     * @ORM\OneToMany(targetEntity="Team", mappedBy="group")
	 */
	protected $teams;
 	
	/**
	 * @ORM\OneToMany(targetEntity="Round", mappedBy="group")
	 */
	protected $rounds;
	

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getTournament(){
		return $this->tournament;
	}

	public function setTournament($tournament){
		$this->tournament = $tournament;
	}

	public function getNumber(){
		return $this->number;
	}

	public function setNumber($number){
		$this->number = $number;
	}

	public function getTeams(){
		return $this->teams;
	}

	public function getRounds(){
		return $this->rounds;
	}
	
	public function getMaxRoundNumber(){
		$max = 0;
		foreach($this->getRounds() as $round){
			$max = max($round->getNumber(), $max);
		}
		return $max;
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
				'name'       => 'number',
				'required'   => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			)));
			
			$this->inputFilter = $inputFilter;        
		}

		return $this->inputFilter;
	}
	
	public function alreadyPlayed(Team $t1, Team $t2){
		foreach($this->getRounds() as $round){
			if($round->alreadyPlayed($t1, $t2))
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
			"tournament" => $this->getTournament(),
			"number" => $this->getNumber(),
			"teams" => $this->getTeams(),
			"rounds" => $this->getRounds(),
		);
		return $data;
	}
}