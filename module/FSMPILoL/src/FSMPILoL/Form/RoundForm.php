<?php
namespace FSMPIVideo\Form;

use Zend\Form\Form;

class LecturerForm extends Form
{
	public function __construct(\Doctrine\ORM\EntityManager $em){
		// we want to ignore the name passed
		parent::__construct('lecturer');
		
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'number',
			'type' => 'Text',
			'options' => array(
				'label' => 'Number',
			),
			'attributes' => array(
				'id' => 'round_number',
				'class' => 'form-control',
			)
		));
		
		$this->add(array(
			'name' => 'parameter',
			'type' => 'Textarea',
			'options' => array(
				'label' => 'Parameter',
			),
			'attributes' => array(
				'id' => 'round_parameter',
				'class' => 'form-control',
			)
		));
	
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Go',
				'id' => 'submitbutton',
				'class' => 'btn btn-primary'
			),
		));

	}
}