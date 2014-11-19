<?php
namespace FSMPILoL\Form;

use Zend\Form\Form;
use FSMPILoL\Entity\Match;

class ResultPaarungForm extends Form{
	public function __construct(){
		// we want to ignore the name passed
		parent::__construct('setresult');
		
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'pointsHome',
			'type' => 'Number',
			'options' => array(
				'label' => 'Points Home',
			),
			'attributes' => array(
				'id' => 'setresult_home',
				'class' => 'form-control',
				'interval' => '1',
				'min' => 0,
			)
		));
		
		$this->add(array(
			'name' => 'pointsGuest',
			'type' => 'Number',
			'options' => array(
				'label' => 'Points Guest',
			),
			'attributes' => array(
				'id' => 'setresult_guest',
				'class' => 'form-control',
				'interval' => '1',
				'min' => 0,
			)
		));
		
		$this->add(array(
			'name' => 'anmerkung',
			'type' => 'Textarea',
			'options' => array(
				'label' => 'Anmerkung',
			),
			'attributes' => array(
				'id' => 'setresult_comment',
				'class' => 'form-control',
			)
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Ergebnis setzen',
				'id' => 'setresult_submit',
				'class' => 'btn btn-success',
			),
		));

	}
}