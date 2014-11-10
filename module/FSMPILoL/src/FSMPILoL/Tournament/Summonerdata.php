<?php
namespace FSMPILoL\Tournament;

use FSMPILoL\Riot\RiotAPI;

class Summonerdata {
	protected $anmeldung;
	protected $rankedWins;
	protected $normalWins;
	protected $tier;
	protected $level;
	protected $profileIconId;
	
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
		$this->profileIconID = $summoner->profileIconId;
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
}