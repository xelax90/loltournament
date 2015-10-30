<?php

namespace FSMPILoL\Tournament;

use FSMPILoL\Entity\Tournament as TournamentEntity;

interface TournamentAwareInterface {
	
	/**
	 * Set tournament
	 * @param TournamentEntity $tournament
	 */
	public function setTournament(TournamentEntity $tournament);
	
	/**
	 * return tournament
	 * @return TournamentEntity
	 */
	public function getTournament();
	
}
