<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Form;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of AnmeldungSingleForm
 *
 * @author schurix
 */
class AnmeldungSingleForm extends Form implements InputFilterProviderInterface{

	public function __construct(){
		// we want to ignore the name passed
		parent::__construct('anmeldung_single');
		
		$this->setAttribute('method', 'post')
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setInputFilter(new InputFilter());
		
		$this->add(array(
			'name' => 'anmeldung',
            'type' => 'FSMPILoL\Form\AnmeldungFieldset',
            'options' => array(
                'use_as_base_fieldset' => true,
				'showSub' => true,
				'showAnmerkungen' => true,
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
				'label_options' => array(
					'disable_html_escape' => true,
				),
			),
			'attributes' => array(
				'value' => 'Eingaben Prüfen',
			),
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Eingaben Prüfen',
			),
		));

	}

	public function getInputFilterSpecification() {
		$filters = array(
			'ausschreibung_gelesen' => array(
				'required' => true,
			)
		);
		
		return $filters;
	}

}