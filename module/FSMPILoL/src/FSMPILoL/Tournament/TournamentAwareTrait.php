<?php
namespace FSMPILoL\Tournament;

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
	 * @param Tournament $tournament
	 */
	public function setTournament(Tournament $tournament) {
		$this->tournament = $tournament;
	}
	
	/**
	 * @return Tournament
	 */
	public function getTournament(){
		return $this->tournament;
	}
}
