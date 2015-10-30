<?php

namespace FSMPILoL\Tournament;

interface TournamentAwareInterface {
	
	/**
	 * Set tournament
	 * @param Tournament $tournament
	 */
	public function setTournament(Tournament $tournament);
	
	/**
	 * return tournament
	 * @return Tournament
	 */
	public function getTournament();
	
}
