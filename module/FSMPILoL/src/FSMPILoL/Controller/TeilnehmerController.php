<?php

namespace FSMPILoL\Controller;

use FSMPILoL\Tournament\Anmeldung;
use Zend\View\Model\ViewModel;
/**
 * Description of TeilnehmerController
 *
 * @author schurix
 */
class TeilnehmerController extends AbstractTournamentFrontendController{
	public function getAnmeldung(){
		return $this->getTournamentService()->getAnmeldung();
	}
	
	public function teilnehmerAction(){
		$this->getTournamentService()->setAPIData();
		
		$anmeldung = $this->getAnmeldung();
		$singles = $anmeldung->getSingles();
		$teams = $anmeldung->getTeams();
		$loginForm = $this->getLoginForm();
		
		return new ViewModel(array('singles' => $singles, 'teams' => $teams, 'loginForm' => $loginForm));
	}
}
