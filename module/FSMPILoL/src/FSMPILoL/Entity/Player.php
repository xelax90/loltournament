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
	
	// Data loaded by api
	protected $level;
	protected $tier;
	protected $normalWins;
	protected $rankedWins;
	protected $profileIconId;
	
	protected $score;
	
	public function __construct($anmeldung = null, $team = null, $isCaptain = null, $summoner = null, $api = null){
		if($anmeldung !== null)
			$this->anmeldung = $anmeldung;
		
		if($team !== null)
			$this->team = $team;
		
		if($isCaptain !== null)
			$this->isCaptain = $isCaptain;
		
		if($summoner !== null && $api !== null)
			$this->setAPIData($summoner, $api);
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
	
	public function getLevel(){
		return $this->level;
	}
	
	public function getTier(){
		return $this->level;
	}
	
	public function getNormalWins(){
		return $this->level;
	}
	
	public function getRankedWins(){
		return $this->level;
	}
	
	public function getProfileIconId(){
		return $this->level;
	}
	
	public function setAPIData($summoner, $api){
		$stats = $api->getStats($summoner->id);
		
		// get tier
		$leagueEntries = $api->getLeagueEntry($summoner->id);
		$leagueEntry = 404;
		if(!is_numeric($leagueEntries)){
			$leagueEntries = $leagueEntries->{$summoner->id};
			foreach($leagueEntries as $entry){
				//var_dump($entry);
				if($entry->queue == "RANKED_SOLO_5x5" && !empty($entry->entries))
					$leagueEntry = $entry;
			}
		}
		$league = "Unranked";
		if(!is_numeric($leagueEntry))
			$league = ucfirst(strtolower($leagueEntry->tier))." ".$leagueEntry->entries[0]->division;
		elseif($leagueEntry != 404)
			$league = "?";
		
		// ranked/normal wins
		$rankedWins = 0;
		$normalWins = 0;
		foreach($stats->playerStatSummaries as $summary){
			if($summary->playerStatSummaryType == "RankedSolo5x5")
				$rankedWins = $summary->wins;
			elseif($summary->playerStatSummaryType == "Unranked")
				$normalWins = $summary->wins;
		}
		
		$this->level = $summoner->summonerLevel;
		$this->tier = $league;
		$this->normalWins = $normalWins;
		$this->rankedWins = $rankedWins;
		$this->profileIconId = $summoner->profileIconId;
		
		if(!$this->getSummonerId())
			$this->setSummonerId($summoner->id);
		
		$this->getScore(true);
	}
	
	public function getScore($refresh = false){
		if(null == $this->score || $refresh){
			$score = 0;
			switch(strtolower(substr($this->tier, 0, 3))){
				case "bro" : $score += 2; break;
				case "sil" : $score += 3; break;
				case "gol" : $score += 4; break;
				case "pla" : $score += 5; break;
				case "dia" : $score += 6; break;
				case "mas" : $score += 7; break;
				case "cha" : $score += 7; break;
				default:
					$score += 1; 
					if($this->level < 30) 
						break;
					if($this->normalWins >= 300)
						$score += 1;
					if($this->normalWins >= 600)
						$score += 1;
					if($this->normalWins >= 1000)
						$score += 1;
					break;
			}
			$this->score = $score;
		}
		return $this->score;
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