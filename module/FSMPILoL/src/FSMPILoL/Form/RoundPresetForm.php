<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Form;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorInterface;
use FSMPILoL\Options\RoundCreatorOptions;

/**
 * Description of RoundPresetForm
 *
 * @author schurix
 */
class RoundPresetForm extends Form{
	public function __construct(ServiceLocatorInterface $sl){
		// we want to ignore the name passed
		parent::__construct('roundpeset');
		
		/* @var $options RoundCreatorOptions */
		$options = $sl->get('FSMPILoL\Options\RoundCreator');
		$types = $options->getRoundTypes();
		
		$typeSelect = array();
		foreach($types as $type => $value){
			$typeSelect[$type] = ucfirst($type);
		}
		$this->add(array(
			'name' => 'preset',
			'type' => 'Select',
			'options' => array(
				'label' => 'Typ',
				'options' => $typeSelect,
			),
			'attributes' => array(
				'id' => 'preset_preset',
				'class' => 'form-control',
			)
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Standardwerte eintragen',
				'id' => 'preset_submitbutton',
				'class' => 'btn btn-primary'
			),
		));
	}
}
