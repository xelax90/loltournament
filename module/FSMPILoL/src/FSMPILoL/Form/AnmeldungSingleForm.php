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
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of AnmeldungSingleForm
 *
 * @author schurix
 */
class AnmeldungSingleForm extends Form implements ObjectManagerAwareInterface, InputFilterProviderInterface{
	protected $em;

	public function __construct($name = "", $options = array()){
		// we want to ignore the name passed
		parent::__construct('anmeldung_single', $options);
		$this->setAttribute('method', 'post');
	}
	
	public function init(){
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()));
		
		$this->add(array(
			'name' => 'anmeldung',
            'type' => 'FSMPILoL\Form\AnmeldungFieldset',
            'options' => array(
                'use_as_base_fieldset' => true,
				'showSub' => false,
				'showAnmerkungen' => true,
				'requireRWTH' => true,
            ),
			'attributes' => array(
				'class' => 'anmeldung_single_fieldset',
			)
        ));

		$this->add(array(
			'name' => 'ausschreibung_gelesen',
			'type' => 'Checkbox',
			'options' => array(
				'label' => 'Ausschreibung gelesen<sup>*</sup>',
				'checked_value' => '1',
				'label_options' => array(
					'disable_html_escape' => true,
				),
				'column-size' => 'sm-10 col-sm-offset-2',
			),
			'attributes' => array(
			),
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Eingaben Prüfen',
			),
			'options' => array(
				'as-group' => true,
			)
		));
	}

	public function getInputFilterSpecification() {
		
		$filters = array(
			'ausschreibung_gelesen' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'Digits'),
				),
				'validators' => array(
					array(
						'name' => 'Callback',
						'options' => array(
							'callback' => function($v){
								return !empty($v);
							},
							'message' => 'Du musst die Ausschreibung lesen, um am Turnier teilzunehmen',
						)
					)
				)
			),
		);
		
		return $filters;
	}
	
	public function getObjectManager() {
		return $this->em;
	}

	public function setObjectManager(ObjectManager $objectManager) {
		$this->em = $objectManager;
	}
}