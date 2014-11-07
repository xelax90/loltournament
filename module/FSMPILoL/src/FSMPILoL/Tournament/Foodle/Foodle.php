<?php
namespace FSMPILoL\Tournament\Foodle;

class Foodle{
	protected $name;
	protected $description;
	protected $expire;
	protected $cols;
	
	protected static $requestURL = 'https://terminplaner.dfn.de/schedule.php';
	
	public function __construct($name, $description, $expire, $cols){
		$this->name = $name;
		$this->description = $description;
		$this->expire = $expire;
		$this->cols = $cols;
	}
	
	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getDescription(){
		return $this->description;
	}

	public function setDescription($description){
		$this->description = $description;
	}

	public function getExpire(){
		return $this->expire;
	}

	public function setExpire($expire){
		$this->expire = $expire;
	}

	public function getCols(){
		return $this->cols;
	}

	public function setCols($cols){
		$this->cols = $cols;
	}

	public static function getPollForGame($match){
		$round = $match->getRound();
		$group = $round->getGroup();
		$tournament = $group->getTournament();
		
		$name = $tournament->getName();
		if($match)
			$description = "Gruppe ".$group->getNumber().", Runde ".$round->getNumber().": ".$match->getTeamHome()->getName(). " - ".$match->getTeamGuest()->getName();
		
		$date = new DateTime();
		$date->modify("+".($round->getDuration() + 5)." days");
		$expire = $date->format("Y-m-d H:i");
		
		$times = array("10:00", "12:00", "14:00", "16:00", "18:00", "19:00", "20:00", "21:00", "22:00", "23:00");
		$cols = array();
		
		$now = new DateTime($round->getStartDate());
		
		$end = new DateTime($round->getStartDate());
		$end->modify("+".$round->getDuration()." days");
		$end->modify("+12 hours");
		
		while($now < $end){
			$cols[] = new FoodleCol($now->format("d. M Y"), $times);
			$now->modify("+1 day");
		}
		
		return new LoLFoodle($name, $description, $expire, $cols);
	}
	
	public function create(){
		$parameters = array(
			"name" => $this->getName(),
			"expire" => $this->getExpire(),
			"descr" => $this->getDescription(),
			"anon" => "",
			"coldef" => implode('|', $this->getCols()),
			"save" => "Terminplan veröffentlichen"
		);
		
		$options = array(
		    'https' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($parameters),
		    ),
			'http' => array(
			    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			    'method'  => 'POST',
			    'content' => http_build_query($parameters),
			),
		);
		
		$context  = stream_context_create($options);
		$result = file_get_contents(self::$requestURL, false, $context);
		
		// get input contining foodle url
		$inputstart = strpos($result, '<input type="text"');
		$inputend = strpos($result, '" />', $inputstart) + 2;
		$input = substr($result, $inputstart, $inputend-$inputstart);
		//var_dump($input);
		
		// get url
		$start = strpos($input, 'value="');
		$start = strpos($input, '"', $start);
		$end = strpos($input, '" ', $start);
		$url = substr($input, $start+1, $end-$start - 1);
		
		return $url;
	}
}
