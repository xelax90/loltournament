<?php
namespace FSMPILoL\Entity;

use Doctrine\ORM\Mapping as ORM;
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
class Game implements JsonSerializable
{
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
	 * @ORM\Column(type="string", nullable=true);
	 */
	protected $meldungHome;
 	
	/**
	 * @ORM\Column(type="string", nullable=true);
	 */
	protected $meldungGuest;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $anmerkungHome;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $anmerkungGuest;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $screenHome;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $screenGuest;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $gameLinkHome;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $gameLinkGuest;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $report;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $streamLink;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $tournamentCode;
 	

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
	
	public function getTournamentCode(){
		return $this->tournamentCode;
	}

	public function setTournamentCode($tournamentCode){
		$this->tournamentCode = $tournamentCode;
	}

	public function getStreamLink(){
		return $this->streamLink;
	}

	public function setStreamLink($streamLink){
		$this->streamLink = $streamLink;
	}
	
	function getGameLinkHome() {
		return $this->gameLinkHome;
	}

	function getGameLinkGuest() {
		return $this->gameLinkGuest;
	}

	function setGameLinkHome($gameLinkHome) {
		$this->gameLinkHome = $gameLinkHome;
	}

	function setGameLinkGuest($gameLinkGuest) {
		$this->gameLinkGuest = $gameLinkGuest;
	}

		
	public function generateTournamentCode(){
		
		// Keine Codes für Spielfrei
		if($this->getTeamBlue() == null || $this->getTeamPurple() == null)
			return;
		
		$tournamentName = $this->getMatch()->getRound()->getGroup()->getTournament()->getName();
		
		$url = 'pvpnet://lol/customgame/joinorcreate';
		$maps = array('map1' => "Summoners Rift", 'map10' => "Twisted Treeline", 'map8' => 'Crystal Scar', 'map12' => 'Howling Abyss');
		$picks = array('pick1' => 'Blind Pick', 'pick2' => "Draft Mode", 'pick4' => "All Random", 'pick6' => 'Tournament Draft');
		
		$url .= '/map1'; // map
		$url .= '/pick6'; // pick type
		$url .= '/team5'; // 5 Players per team
		$url .= '/specALL'; // allow spectate all (specLOBBY / specNONE for restriction)
		
		$bytes = openssl_random_pseudo_bytes(5);
		$password = bin2hex($bytes);
		//$password = "bbabababa";
		
		$data = array(
			"name" => $tournamentName.PHP_EOL.
						"Rd. ".$this->getMatch()->getRound()->getNumber(). ", Match ".$this->getMatch()->getNumber(). ", Spiel ".$this->getNumber().PHP_EOL.
						$this->getTeamBlue()->getName() . " - ".$this->getTeamPurple()->getName(),
			"extra" => $this->getId()."_".$this->getNumber(),
			"password" => $password,
			"report" => "http://lol.fsmpi.rwth-aachen.de/gamereport.html"
		);
		
		$code = $url.'/'.base64_encode(json_encode($data));
		$this->setTournamentCode($code);
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