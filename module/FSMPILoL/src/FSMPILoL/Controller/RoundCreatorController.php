<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Controller;

use Zend\View\Model\ViewModel;
use FSMPILoL\Form\RoundForm;
use FSMPILoL\Form\RoundPresetForm;
use FSMPILoL\Tournament\Group;
use FSMPILoL\Tournament\RoundCreator\AbstractRoundCreator;

/**
 * Description of RoundCreatorController
 *
 * @author schurix
 */
class RoundCreatorController extends AbstractTournamentAdminController{
	protected $creatorOptions;
	
	public function indexAction() {
		$tournament = $this->getTournament();
		return new ViewModel(array('tournament' => $tournament));
	}
	
	protected function _redirectToRunden(){
		return $this->redirect()->toRoute('zfcadmin/runden');
	}
	
	/**
	 * 
	 * @return \FSMPILoL\Options\RoundCreatorOptions
	 */
	protected function getCreatorOptions(){
		if(null === $this->creatorOptions){
			$this->creatorOptions = $this->getServiceLocator()->get('FSMPILoL\Options\RoundCreator');
		}
		return $this->creatorOptions;
	}
	
	public function setpresetAction(){
		$em = $this->getEntityManager();
		
		$group_id = $this->getEvent()->getRouteMatch()->getParam('group_id');
		/* @var $group \FSMPILoL\Entity\Group */
		$group = $em->getRepository('FSMPILoL\Entity\Group')->find($group_id);
		if(empty($group)){
			return $this->_redirectToRunden();
		}
		
        /* @var $request \Zend\Http\Request */
        $request = $this->getRequest();

		$creatorOptions = $this->getCreatorOptions();
		$types = $creatorOptions->getRoundTypes();
		if($request->isPost()){
			/* @var $data \Zend\Stdlib\Parameters */
			$data = $request->getPost();
			if(!empty($data['preset']) && in_array($data['preset'], array_keys($types))){
				return $this->redirect()->toRoute('zfcadmin/runden/create', array('group_id' => $group->getId(), 'preset' => $data['preset']));
			}
		}
		return $this->redirect()->toRoute('zfcadmin/runden/create', array('group_id' => $group->getId()));
	}
	
	
	public function createAction(){
		$em = $this->getEntityManager();
		
		$group_id = $this->getEvent()->getRouteMatch()->getParam('group_id');
		/* @var $group \FSMPILoL\Entity\Group */
		$group = $em->getRepository('FSMPILoL\Entity\Group')->find($group_id);
		if(empty($group)){
			return $this->_redirectToRunden();
		}
		$gGroup = new Group($group, $this->getServiceLocator());
		
		$form = new RoundForm($this->getServiceLocator());
		$form->remove('id');
		$form->remove('number');
		$form->remove('isHidden');
		$form->get('submit')->setAttribute('value', 'Auslosen');
		
		$creatorOptions = $this->getCreatorOptions();
		$types = $creatorOptions->getRoundTypes();
		$preset = $this->getEvent()->getRouteMatch()->getParam('preset');
		if(in_array($preset, array_keys($types))){
			$creator = AbstractRoundCreator::getInstance($gGroup, $preset, $this->getServiceLocator());
			if($creator){
				$defaults = $creator->getDefaultProperties();
				$data = array();
				foreach($defaults as $key => $value){
					$data['properties_'.$key] = $value;
				}
				$form->setData($data);
			}
		} else {
			$preset = '';
		}

        /* @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$data = $form->getData();
				$creator = AbstractRoundCreator::getInstance($gGroup, $data['type'], $this->getServiceLocator());
				$properties = array();
				foreach($data as $key => $value){
					if(strpos($key, 'properties_') === 0){
						$properties[str_replace('properties_', '', $key)] = $value;
					}
				}
				$creator->nextRound($group, new \DateTime($data['startDate']), $properties, true, $data['duration'], $data['timeForDates']);
				return $this->_redirectToRunden();
			}
        }
		
		$presetForm = new RoundPresetForm($this->getServiceLocator());
		
		return new ViewModel(array('group' => $group, 'form' => $form, 'presetForm' => $presetForm, 'preset' => $preset));
	}
}
