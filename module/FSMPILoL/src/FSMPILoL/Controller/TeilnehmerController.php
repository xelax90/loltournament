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
	/** @var Anmeldung */
	protected $anmeldung;
	
	public function getAnmeldung(){
		if(null === $this->anmeldung){
			$this->anmeldung = $this->getServiceLocator()->get(Anmeldung::class);
		}
		return $this->anmeldung;
	}
	
	public function teilnehmerAction(){
		$this->setAPIData();
		
		$anmeldung = $this->getAnmeldung();
		$singles = $anmeldung->getSingles();
		$teams = $anmeldung->getTeams();
		$loginForm = $this->getLoginForm();
		
		return new ViewModel(array('singles' => $singles, 'teams' => $teams, 'loginForm' => $loginForm));
	}
}
