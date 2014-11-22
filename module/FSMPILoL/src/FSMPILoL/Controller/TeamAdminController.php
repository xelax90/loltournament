<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Controller;

use Zend\View\Model\ViewModel;
use FSMPILoL\Form\CommentPaarungForm;
use FSMPILoL\Entity\User;

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
	
	protected function _redirectToTeams(){
		return $this->redirect()->toRoute('zfcadmin/teams');
	}
	
	public function anmerkungAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToTeams();
		}
        $team_id = $this->getEvent()->getRouteMatch()->getParam('team_id');
		$em = $this->getEntityManager();
		/* @var $team \FSMPILoL\Entity\Team */
		$team = $em->getRepository('FSMPILoL\Entity\Team')->find((int)$team_id);
		if(!$team){
			return $this->_redirectToTeams();
		}
		$form = new CommentPaarungForm();
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$data = $form->getData();
				$team->setAnmerkung($data['anmerkung']);
				$em->flush();
				return $this->_redirectToTeams();
			}
        }
		return new ViewModel(array('id' => $team->getId(), 'form' => $form));
	}
	
	public function blockAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToTeams();
		}
        $team_id = $this->getEvent()->getRouteMatch()->getParam('team_id');
		$em = $this->getEntityManager();
		/* @var $team \FSMPILoL\Entity\Team */
		$team = $em->getRepository('FSMPILoL\Entity\Team')->find((int)$team_id);
		if(!$team){
			return $this->_redirectToTeams();
		}
		
		$identity = $this->zfcUserAuthentication()->getIdentity();
		if($identity->getRole() <= User::ROLE_ADMIN || $team->getAnsprechpartner() == $identity){
			$team->setIsBlocked(true);
			$em->flush();
		}
		return $this->_redirectToTeams();
	}
	
	public function unblockAction(){
		$tournament = $this->getTournament();
		if(!$tournament){
			return $this->_redirectToTeams();
		}
        $team_id = $this->getEvent()->getRouteMatch()->getParam('team_id');
		$em = $this->getEntityManager();
		/* @var $team \FSMPILoL\Entity\Team */
		$team = $em->getRepository('FSMPILoL\Entity\Team')->find((int)$team_id);
		if(!$team){
			return $this->_redirectToTeams();
		}
		
		$team->setIsBlocked(false);
		$em->flush();
		return $this->_redirectToTeams();
	}
}
