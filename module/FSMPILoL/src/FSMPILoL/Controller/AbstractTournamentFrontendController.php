<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use FSMPILoL\Entity\Tournament as TournamentEntity;
use FSMPILoL\Tournament\Tournament;

/**
 * Description of AbstractTournamentFrontendController
 *
 * @author schurix
 */
abstract class AbstractTournamentFrontendController extends AbstractActionController{
	/** @var EntityManager */
	protected $entityManager;
	
	/** @var TournamentEntity */
	protected $tournament;
	
	/** @var Tournament */
	protected $tournamentService;
	
	/**
	 * @return EntityManager
	 */
	public function getEntityManager(){
		if (null === $this->entityManager) {
			$this->entityManager = $this->getServiceLocator()->get(EntityManager::class);
		}
		return $this->entityManager;
	}
	
	/**
	 * @return TournamentEntity
	 */
	public function getTournament(){
		return $this->getTournamentService()->getTournament();
	}
	
	public function getTournamentService(){
		if(null === $this->tournamentService){
			$this->tournamentService = $this->getServiceLocator()->get(Tournament::class);
		}
		return $this->tournamentService;
	}
	
	protected function getLoginForm(){
		$loginForm = $this->getServiceLocator()->get('zfcuser_login_form');
		$fm = $this->flashMessenger()->setNamespace('zfcuser-login-form')->getMessages();
		if (isset($fm[0])) {
			$loginForm->setMessages(
				array('identity' => array($fm[0]))
			);
		}
		return $loginForm;
	}
	
	protected function authenticate(){
		if($this->zfcUserAuthentication()->hasIdentity()){
			return true;
		}
		
        $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        $result = $adapter->prepareForAuthentication($this->getRequest());
        $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);
		return $auth;
	}
}
