<?php
namespace FSMPILoL\Form;

use Zend\Form\Form;

class TournamentForm extends Form
{
	public function __construct(){
		// we want to ignore the name passed
		parent::__construct('tournament');
		
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'options' => array(
				'label' => 'Name',
			),
			'attributes' => array(
				'id' => 'tournament_name',
				'class' => 'form-control',
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