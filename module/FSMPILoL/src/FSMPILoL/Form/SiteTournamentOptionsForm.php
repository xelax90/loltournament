<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Form;

use Zend\Form\Form;

/**
 * SiteTournamentOptionsForm
 *
 * @author schurix
 */
class SiteTournamentOptionsForm extends Form{
	
	public function __construct($name = "", $options = array()){
		// we want to ignore the name passed
		parent::__construct('SiteTournamentOptionsForm', $options);
		$this->setAttribute('method', 'post');
	}
	
	public function init(){
		$this->add(array(
			'name' => 'config',
            'type' => SiteTournamentOptionsFieldset::class,
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
}
