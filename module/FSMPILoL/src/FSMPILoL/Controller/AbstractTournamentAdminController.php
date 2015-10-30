<?php

namespace FSMPILoL\Controller;

use FSMPILoL\Tournament\Group;
use FSMPILoL\Tournament\Tournament;

/**
 * Description of AbstractTournamentAdminController
 *
 * @author schurix
 */
class AbstractTournamentAdminController extends AbstractAdminController{
	/** @var Tournament */
	protected $tournament;
	
	/**
	 * 
	 * @return Tournament
	 */
	public function getTournament(){
		if(null === $this->tournament){
			$this->tournament = $this->getServiceLocator()->get(Tournament::class);
		}
		return $this->tournament;
	}
}
