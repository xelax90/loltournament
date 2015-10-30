<?php
namespace FSMPILoL\Tournament;

use FSMPILoL\Entity\Tournament as TournamentEntity;

/**
 * Provides implementation of TournamentAwareInterface
 *
 * @author schurix
 */
trait TournamentAwareTrait {
	
	/**
	 * @var TournamentEntity
	 */
	protected $tournament;
	
	/**
	 * @param TournamentEntity $tournament
	 */
	public function setTournament(TournamentEntity $tournament) {
		$this->tournament = $tournament;
	}
	
	/**
	 * @return TournamentEntity
	 */
	public function getTournament(){
		return $this->tournament;
	}
}
