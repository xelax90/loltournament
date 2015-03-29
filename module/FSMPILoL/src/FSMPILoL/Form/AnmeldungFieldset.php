<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Form;

use FSMPILoL\Entity\Anmeldung;
use Zend\Form\InputFilterProviderFieldset;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of AnmeldungForm
 *
 * @author schurix
 */
class AnmeldungFieldset extends Fieldset implements InputFilterProviderInterface{
	
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'anmeldung';
		}
		parent::__construct($name, $options);
		
		$this//->setHydrator(new ClassMethodsHydrator(false))
				->setObject(new Anmeldung());
		
		$this->addUserfields();
		//$this->setFilters();
	}
	
	public function setOptions($options) {
		parent::setOptions($options);
		
		if(isset($options['showSub']) && !$options['showSub']){
			$this->remove('isSub');
		}
		
		if(isset($options['showAnmerkungen']) && !$options['showAnmerkungen']){
			$this->remove('anmerkung');
		}
		
	}
	
	protected function addUserfields() {
		$this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'options' => array(
				'label' => 'Name<sup>*</sup>',
				'label_options' => array(
					'disable_html_escape' => true,
				),
			),
			'attributes' => array(
				'class' => 'input_container',
				'id' => "",
			)
		));
		
		$this->add(array(
			'name' => 'email',
			'type' => 'Email',
			'options' => array(
				'label' => 'Email<sup>*</sup>',
				'label_options' => array(
					'disable_html_escape' => true,
				),
			),
			'attributes' => array(
				'class' => 'input_container',
				'id' => "",
			)
		));

		$this->add(array(
			'name' => 'facebook',
			'type' => 'Text',
			'options' => array(
				'label' => 'Facebook',
			),
			'attributes' => array(
				'class' => 'input_container',
				'id' => "",
			)
		));

		$this->add(array(
			'name' => 'otherContact',
			'type' => 'Text',
			'options' => array(
				'label' => 'Weitere Kontaktdaten',
			),
			'attributes' => array(
				'class' => 'input_container',
				'id' => "",
			)
		));

		$this->add(array(
			'name' => 'summonerName',
			'type' => 'Text',
			'options' => array(
				'label' => 'Beschwörer Name<sup>*</sup>',
				'label_options' => array(
					'disable_html_escape' => true,
				),
			),
			'attributes' => array(
				'class' => 'input_container',
				'id' => "",
			)
		));
		
		$this->add(array(
			'name' => 'isSub',
			'type' => 'Select',
			'options' => array(
				'label' => 'Ersatzspieler<sup>*</sup>',
				'options' => array('-1' => 'Bitte Wählen', '2' => 'Ja, ich möchte NUR Ersatzspieler sein', '1' => 'Ja, ich könnte auch Ersatzspieler sein', '0' => "Nein, ich möchte kein Ersatzspieler sein."),
				'label_options' => array(
					'disable_html_escape' => true,
				),
			),
			'attributes' => array(
				'id' => "",
			)
		));
		
		$this->add(array(
			'name' => 'anmerkung',
			'type' => 'Textarea',
			'options' => array(
				'label' => 'Anmerkungen',
			),
			'attributes' => array(
				'id' => "",
			)
		));
	}
	
	/**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     \*/
    public function getInputFilterSpecification(){
		$filters = array();
		
		$dataRequired = true;
		if(isset($this->getOptions()['data_required']) && !$this->getOption('data_required')){
			$dataRequired = false;
		}
		$textFields = array('name' => true, 'email' => true, 'facebook' => false, 'otherContact' => false, 'summonerName' => true, 'anmerkung' => false);
		foreach($textFields as $field => $required){
			$filters[$field] = array(
				'required' => $required && $dataRequired,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => 'XelaxHTMLPurifier\Filter\HTMLPurifier'),
				),
			);
		}
		
		$filters['isSub'] = array(
			'required' => false,
			'filters' => array(
				array('name' => 'Int'),
			),
			'validators' => array(
				array('name' => 'Between', array('min' => 0, 'max' => 2))
			)
		);
		
		return $filters;
	}
}