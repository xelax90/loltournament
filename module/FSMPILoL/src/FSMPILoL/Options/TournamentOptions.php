<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Options;

use XelaxSiteConfig\Options\AbstractSiteOptions;

/**
 * Description of TournamentOptions
 *
 * @author schurix
 */
class TournamentOptions extends AbstractSiteOptions{
	
	const TOURNAMENT_PHASE_ANNOUNCED = 'announced';
	const TOURNAMENT_PHASE_REGISTRATION = 'registration';
	const TOURNAMENT_PHASE_PRE_ROUND = 'preRound';
	const TOURNAMENT_PHASE_MAIN_ROUND = 'mainRound';
	const TOURNAMENT_PHASE_PLAYOFFS = 'playoffs';
	
	const REGISTRATION_STATUS_OPEN = 'open';
	const REGISTRATION_STATUS_CLOSED = 'closed';
	const REGISTRATION_STATUS_NO_TEAMS = 'noTeams';
	const REGISTRATION_STATUS_SUB_ONLY = 'subOnly';
	const REGISTRATION_STATUS_SUB_ONLY_OR_TEAM = 'subOnlyTeam';
	
	protected $currentTournament = 8;
	
	protected $tournamentPhase = self::TOURNAMENT_PHASE_ANNOUNCED;
	
	protected $registrationStatus = self::REGISTRATION_STATUS_CLOSED;
	
	public function getCurrentTournament() {
		return $this->currentTournament;
	}

	public function getTournamentPhase() {
		return $this->tournamentPhase;
	}

	public function getRegistrationStatus() {
		return $this->registrationStatus;
	}

	public function setCurrentTournament($currentTournament) {
		$this->currentTournament = $currentTournament;
		return $this;
	}

	public function setTournamentPhase($tournamentPhase) {
		$this->tournamentPhase = $tournamentPhase;
		return $this;
	}

	public function setRegistrationStatus($registrationStatus) {
		$this->registrationStatus = $registrationStatus;
		return $this;
	}
}
