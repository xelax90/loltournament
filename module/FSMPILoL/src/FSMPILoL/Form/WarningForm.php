<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of WarnPlayerForm
 *
 * @author schurix
 */
class WarningForm  extends Form implements InputFilterProviderInterface {
	public function __construct() {
		parent::__construct('warn_player');
		
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'comment',
			'type' => 'Textarea',
			'options' => array(
				'label' => 'Comment',
			),
			'attributes' => array(
				'id' => '',
				'class' => 'form-control',
			)
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Verwarnen',
				'class' => 'btn btn-success'
			),
		));
	}
	
	public function getInputFilterSpecification() {
		$filter = array(
			'id' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			'warning_id' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			'comment' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			),
		);
		return $filter;
	}
}
