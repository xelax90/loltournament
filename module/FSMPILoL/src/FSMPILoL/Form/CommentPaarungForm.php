<?php
namespace FSMPILoL\Form;

use Zend\Form\Form;

class CommentPaarungForm extends Form implements \Zend\InputFilter\InputFilterProviderInterface{
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

	public function getInputFilterSpecification() {
		$filters = array(
			'id' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'Int'),
				),
			),
			'anmerkung' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			),
		);
		return $filters;
	}

}