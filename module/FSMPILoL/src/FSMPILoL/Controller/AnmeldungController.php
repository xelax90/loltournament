<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Controller;

use Zend\View\Model\ViewModel;
use FSMPILoL\Form\AnmeldungSingleForm;
use FSMPILoL\Form\AnmeldungTeamForm;
use FSMPILoL\Entity\Anmeldung as AnmeldungEntity;
use FSMPILoL\Tournament\Anmeldung;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;


/**
 * Description of AnmeldungController
 *
 * @author schurix
 */
class AnmeldungController extends AbstractTournamentFrontendController {
	/**
	 * @return Anmeldung
	 */
	public function getAnmeldung(){
		return $this->getTournamentService()->getAnmeldung();
	}

	/**
	 * Creates form with name $name
	 * @param string $name
	 * @return \Zend\Form\Form
	 */
	protected function getForm($name){
		$form = $this->getServiceLocator()->get('FormElementManager')->get($name);
		return $form;
	}
	
	protected function _forwardToForm(){
		$match = $this->getEvent()->getRouteMatch()->getParams();
		$match['action'] = 'form';
		return $this->forward()->dispatch($match['controller'], $match);
	}
	
	protected function _forwardToConfirm(){
		$match = $this->getEvent()->getRouteMatch()->getParams();
		$match['action'] = 'confirm';
		return $this->forward()->dispatch($match['controller'], $match);
	}
	
	protected function _redirectToForm(){
		$match = $this->getEvent()->getRouteMatch()->getParams();
		$match['action'] = 'confirm';
		return $this->redirect()->toRoute('anmeldung/form');
	}
	
	protected function getFormOrRedirect(){
		$request = $this->getRequest();
		
		if(!$request->isPost()){
			return $this->_redirectToForm();
		}
		
		$data = $request->getPost();
		if(isset($data['teamName'])){
			$form = $this->getForm(AnmeldungTeamForm::class);
		} elseif(isset($data['anmeldung'])){
			$form = $this->getForm(AnmeldungSingleForm::class);
		} else {
			return $this->_redirectToForm();
		}
		
		$form->setData($data);
		if(!$form->isValid()){
			return $this->_forwardToForm();
		}
		return $form;
	}

	public function formAction(){
		$this->authenticate();
		$lastPlayer = null;
		if($this->zfcUserAuthentication()->hasIdentity()){
			/* @var $identity \FSMPILoL\Entity\User */
			$identity = $this->zfcUserAuthentication()->getIdentity();
			$lastPlayer = $identity->getMostRecentPlayer();
		}
		
		$request = $this->getRequest();
		$singleForm = $this->getForm(AnmeldungSingleForm::class);
		$anmeldungEntity = new AnmeldungEntity();
		
		$teamForm = $this->getForm(AnmeldungTeamForm::class);
		$teamForm->prepare();
		$anmeldungCollection = $teamForm->get('anmeldungen');
		/* @var $anmeldungCollection \Zend\Form\Element\Collection */
		$i = 1;
		foreach ($anmeldungCollection as $anmeldungFieldset) {
			$anmeldungFieldset->setLabel('Spieler '.$i);
			$i++;
		}
		
		$data = array();
		if($request->isPost()){
			$data = $request->getPost();
		}
		
		if($lastPlayer && (!$request->isPost() || (!isset($data['teamName']) && !isset($data['anmeldung'])))){
			$anmeldungEntity->setName($lastPlayer->getUser()->getDisplayName());
			$anmeldungEntity->setEmail($lastPlayer->getUser()->getEmail());
			$anmeldungEntity->setFacebook($lastPlayer->getAnmeldung()->getFacebook());
			$anmeldungEntity->setOtherContact($lastPlayer->getAnmeldung()->getOtherContact());
			$anmeldungEntity->setSummonerName($lastPlayer->getAnmeldung()->getSummonerName());
			
			$teamData = array();
			if(!empty($lastPlayer->getTeam())){
				foreach($lastPlayer->getTeam()->getPlayers() as $player){
					$teamData[] = array(
						'name' => $player->getUser()->getDisplayName(),
						'email' => $player->getUser()->getEmail(),
						'facebook' => $player->getAnmeldung()->getFacebook(),
						'otherContact' => $player->getAnmeldung()->getOtherContact(),
						'summonerName' => $player->getAnmeldung()->getSummonerName()
					);
				}
			}
			$teamForm->setData(array(
				'teamName' => $lastPlayer->getTeam()->getName(),
				'anmeldungen' => $teamData,
			));
		}
		$singleForm->bind($anmeldungEntity);
		
		
		$icons = $this->getAnmeldung()->getAvailableIcons();
		
		//$anmeldung = new AnmeldungEntity();
		//$form->bind($anmeldung);

		if ($request->isPost() && (isset($data['teamName']) || isset($data['anmeldung']))) {
			if(isset($data['teamName'])){ // team form
				$teamForm->setData($data);
				if($teamForm->isValid()){
					return $this->_forwardToConfirm();
				}
			} else {
				$singleForm->setData($data);
				if($singleForm->isValid()){
					return $this->_forwardToConfirm();
				}
			}
		}
		
		$loginForm = $this->getLoginForm();
		
		return new ViewModel(array( 'singleForm' => $singleForm, 'teamForm' => $teamForm, 'icons' => $icons, 'loginForm' => $loginForm));
	}
	
	public function confirmAction(){
		$formOrRedirect = $this->getFormOrRedirect();
		if($formOrRedirect instanceof \Zend\Http\PhpEnvironment\Response){
			return $formOrRedirect;
		}
		$form = $formOrRedirect;
		
		$icons = $this->getAnmeldung()->getAvailableIcons();
		
		$loginForm = $this->getLoginForm();
		
		return new ViewModel(array('form' => $form, 'icons' => $icons, 'loginForm' => $loginForm));
	}
	
	public function readyAction(){
		$loginForm = $this->getLoginForm();
		
		$params = array('loginForm' => $loginForm);
		
		// Run validation and get the used form
		$formOrRedirect = $this->getFormOrRedirect();
		if($formOrRedirect instanceof \Zend\Http\PhpEnvironment\Response){
			return $formOrRedirect;
		} elseif($formOrRedirect instanceof AnmeldungSingleForm){
			$anmeldung = $formOrRedirect->getData();
			if(!$anmeldung instanceof AnmeldungEntity){
				return $this->_redirectToForm();
			}
			$anmeldung->setTournament($this->getTournament());
			$em = $this->getEntityManager();
			$em->persist($anmeldung);
			$em->flush();
			$params['anmeldung'] = $anmeldung;
		} elseif($formOrRedirect instanceof AnmeldungTeamForm) {
			$em = $this->getEntityManager();
			$hydrator = new DoctrineHydrator($this->getEntityManager());
			$data = $formOrRedirect->getData();
			$teamName = $data['teamName'];
			$teamIcon = $data['team_icon_text'];
			$anmeldungen = $data['anmeldungen'];
			$team = array();
			foreach ($anmeldungen as $anmeldung){
				$newAnmeldung = new AnmeldungEntity();
				$hydrator->hydrate($anmeldung, $newAnmeldung);
				$newAnmeldung->setTournament($this->getTournament());
				$newAnmeldung->setTeamName($teamName);
				$newAnmeldung->setIcon($teamIcon);
				$em->persist($newAnmeldung);
				$team[] = $newAnmeldung;
			}
			$em->flush();
			$params['team'] = $team;
		} else {
			$this->flashMessenger()->addErrorMessage('Bei der Anmeldung ist ein Fehler aufgetreten. Bitte versuche es erneut oder kontaktiere die Turnierleitung.');
			return $this->_redirectToForm();
		}
		
		return new ViewModel($params);
	}
}
