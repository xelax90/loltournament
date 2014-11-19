<?php
namespace FSMPILoL\Form;

use Zend\Form\Form;
use FSMPILoL\Entity\Match;

class CommentPaarungForm extends Form{
	public function __construct(){
		// we want to ignore the name passed
		parent::__construct('comment');
		
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'anmerkung',
			'type' => 'Textarea',
			'options' => array(
				'label' => 'Anmerkung',
			),
			'attributes' => array(
				'id' => 'comment_comment',
				'class' => 'form-control',
			)
		));

		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Kommentar speichern',
				'id' => 'comment_submit',
				'class' => 'btn btn-success',
			),
		));

	}
}