<?php
namespace FSMPILoL\Tournament;

use FSMPILoL\Riot\RiotAPI;

class Summonerdata {
	protected $anmeldung;
	protected $anmeldung_id;
	protected $rankedWins;
	protected $normalWins;
	protected $tier;
	protected $level;
	protected $profileIconId;
	protected $score;
	
	public function __construct(RiotAPI $api, $anmeldung, $summoner){
		$stats = $api->getStats($summoner->id);
		
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
		
		$rankedWins = 0;
		$normalWins = 0;
		foreach($stats->playerStatSummaries as $summary){
			if($summary->playerStatSummaryType == "RankedSolo5x5")
				$rankedWins = $summary->wins;
			elseif($summary->playerStatSummaryType == "Unranked")
				$normalWins = $summary->wins;
		}
		
		$this->anmeldung = $anmeldung;
		$this->rankedWins = $rankedWins;
		$this->normalWins = $normalWins;
		$this->tier = $league;
		$this->level = $summoner->summonerLevel;
		$this->profileIconId = $summoner->profileIconId;
		$this->getScore(true);
	}
	
	public function getAnmeldung(){
		return $this->anmeldung;
	}

	public function setAnmeldung($anmeldung){
		$this->anmeldung = $anmeldung;
	}

	public function getRankedWins(){
		return $this->rankedWins;
	}

	public function setRankedWins($rankedWins){
		$this->rankedWins = $rankedWins;
	}

	public function getNormalWins(){
		return $this->normalWins;
	}

	public function setNormalWins($normalWins){
		$this->normalWins = $normalWins;
	}

	public function getTier(){
		return $this->tier;
	}

	public function setTier($tier){
		$this->tier = $tier;
	}

	public function getLevel(){
		return $this->level;
	}

	public function setLevel($level){
		$this->level = $level;
	}

	public function getProfileIconId(){
		return $this->profileIconId;
	}

	public function setProfileIconId($profileIconId){
		$this->profileIconId = $profileIconId;
	}
	
	public function getScore($refresh = false){
		if(null == $this->score || $refresh){
			$score = 0;
			switch(strtolower(substr($this->getTier(), 0, 3))){
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
	
	public function __sleep(){
		return array('anmeldung_id', 'rankedWins', 'normalWins', 'tier', 'level', 'profileIconId');
	}
}