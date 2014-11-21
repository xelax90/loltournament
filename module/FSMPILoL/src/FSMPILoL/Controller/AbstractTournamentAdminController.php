<?php

namespace FSMPILoL\Controller;

use FSMPILoL\Tournament\Group;

/**
 * Description of AbstractTournamentAdminController
 *
 * @author schurix
 */
class AbstractTournamentAdminController extends AbstractAdminController{
	/** @var \FSMPILoL\Entity\Tournament */
	protected $tournament;
	
	/**
	 * 
	 * @return \FSMPILoL\Entity\Tournament
	 */
	public function getTournament(){
		if(null === $this->tournament){
			$options = $this->getServiceLocator()->get('FSMPILoL\Options\Anmeldung');
			$tournamentId = $options->getTournamentId();
			$em = $this->getEntityManager();
			$this->tournament = $em->getRepository('FSMPILoL\Entity\Tournament')->find($tournamentId);
		}
		return $this->tournament;
	}
	
	protected function setTeamdata(){
		$tournament = $this->getTournament();
		if (!$tournament) {
			return;
		}

		foreach($tournament->getGroups() as $group){
			$gGroup = new Group($group, $this->getServiceLocator());
			$gGroup->setTeamdata();
		}
	}

	protected function setAPIData(){
		$tournament = $this->getTournament();
		if (!$tournament) {
			return;
		}

		foreach($tournament->getGroups() as $group){
			$gGroup = new Group($group, $this->getServiceLocator());
			$gGroup->setAPIData();
		}
	}
}
