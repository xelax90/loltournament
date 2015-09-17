<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Form;

use Zend\Form\Form;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * TournamentForm
 *
 * @author schurix
 */
class TournamentForm extends Form implements ObjectManagerAwareInterface{
	protected $em;
	
	public function __construct($name = "", $options = array()){
		// we want to ignore the name passed
		parent::__construct('TournamentForm', $options);
		$this->setAttribute('method', 'post');
	}
	
	public function init(){
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
			 ->setInputFilter(new InputFilter());
		
		$this->add(array(
			'name' => 'tournament',
            'type' => 'FSMPILoL\Form\TournamentFieldset',
            'options' => array(
                'use_as_base_fieldset' => true,
            ),
        ));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Save',
				'class' => 'btn-success'
			),
			'options' => array(
				'as-group' => true,
			)
		));
	}
	
	public function getObjectManager() {
		return $this->em;
	}

	public function setObjectManager(ObjectManager $objectManager) {
		$this->em = $objectManager;
	}
}
