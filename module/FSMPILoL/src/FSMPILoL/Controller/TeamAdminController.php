<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Controller;

use Zend\View\Model\ViewModel;

/**
 * Description of TeamAdminController
 *
 * @author schurix
 */
class TeamAdminController extends AbstractTournamentAdminController{
	
	public function indexAction() {
		$tournament = $this->getTournament();
		if(!$tournament){
			return new ViewModel();
		}
		$this->setTeamdata();
		$this->setAPIData();
		
		return new ViewModel(array('tournament' => $tournament));
	}
	
	
}
