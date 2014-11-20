<?php
namespace FSMPILoL\Form;

use Zend\Form\Form;

class GroupForm extends Form
{
	public function __construct(){
		// we want to ignore the name passed
		parent::__construct('group');
		
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'number',
			'type' => 'Number',
			'options' => array(
				'label' => 'Number',
			),
			'attributes' => array(
				'id' => 'group_number',
				'class' => 'form-control',
				'interval' => '1',
				'min' => 1,
			)
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Go',
				'class' => 'btn btn-success'
			),
		));
	}
}