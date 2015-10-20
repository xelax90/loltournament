<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FSMPILoL\Entity\Anmeldung;
use FSMPILoL\Tournament\TournamentAwareInterface;
use FSMPILoL\Entity\Player;

/**
 * Description of AnmeldungForm
 *
 * @author schurix
 */
class AnmeldungFieldset extends Fieldset implements InputFilterProviderInterface, ObjectManagerAwareInterface, TournamentAwareInterface{
	protected $objectManager;
	
	protected $tournament;
	
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'anmeldung';
		}
		parent::__construct($name, $options);
	}
	
	public function init(){
		$this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
				->setObject(new Anmeldung());
		
		$this->addUserfields();
	}
	
	public function setOptions($options) {
		parent::setOptions($options);
		
		if(isset($options['showSub']) && !$options['showSub']){
			if($this->has('isSub')){
				$this->remove('isSub');
			}
		}
		
		if(isset($options['showAnmerkungen']) && !$options['showAnmerkungen']){
			if($this->has('anmerkung')){
				$this->remove('anmerkung');
			}
		}
		
	}
	
	protected function addUserfields() {
		$options = $this->getOptions();
		
		$this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'options' => array(
				'label' => 'Name<sup>*</sup>',
				'label_options' => array(
					'disable_html_escape' => true,
				),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
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
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
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
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
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
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
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
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'class' => 'input_container',
				'id' => "",
			)
		));
		
		if(!isset($options['showSub']) || $options['showSub']){
			$this->add(array(
				'name' => 'isSub',
				'type' => 'Select',
				'options' => array(
					'label' => 'Ersatzspieler<sup>*</sup>',
					'options' => array('-1' => 'Bitte Wählen', '2' => 'Ja, ich möchte NUR Ersatzspieler sein', '1' => 'Ja, ich könnte auch Ersatzspieler sein', '0' => "Nein, ich möchte kein Ersatzspieler sein."),
					'label_options' => array(
						'disable_html_escape' => true,
					),
					'column-size' => 'sm-10',
					'label_attributes' => array(
						'class' => 'col-sm-2',
					),
				),
				'attributes' => array(
					'id' => "",
				)
			));
		}
		
		if(!isset($options['showAnmerkungen']) || $options['showAnmerkungen']){
			$this->add(array(
				'name' => 'anmerkung',
				'type' => 'Textarea',
				'options' => array(
					'label' => 'Anmerkungen',
					'column-size' => 'sm-10',
					'label_attributes' => array(
						'class' => 'col-sm-2',
					),
				),
				'attributes' => array(
					'id' => "",
				)
			));
		}
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
				'validators' => array(),
			);
		}
		
		if(!isset($this->options['validateEmailExists']) || $this->getOption('validateEmailExists')){
			$filters['email']['validators'][] = array(
				'name' => 'FSMPILoL\Validator\NoObjectExistsInTournament',
				'options' => array(
					'object_repository' => $this->getObjectManager()->getRepository(Anmeldung::class),
					'tournament' => $this->getTournament(),
					'fields' => 'email'
				)
			);
		} elseif(!empty($this->options['validateEmailTournament'])){
			$filters['email']['validators'][] = array(
				'name' => 'FSMPILoL\Validator\PlayerEmail',
				'options' => array(
					'object_repository' => $this->getObjectManager()->getRepository(Player::class),
					'tournament' => $this->getTournament(),
					'fields' => 'email'
				)
			);
		}
		
		
		
		if(!isset($this->options['validateSummonerNameExists']) || $this->getOption('validateSummonerNameExists')){
			$filters['summonerName']['validators'][] = array(
				'name' => 'FSMPILoL\Validator\NoObjectExistsInTournament',
				'options' => array(
					'object_repository' => $this->getObjectManager()->getRepository(Anmeldung::class),
					'tournament' => $this->getTournament(),
					'fields' => 'summonerName'
				)
			);
		} elseif(!empty($this->options['validateSummonerNameTournament'])){
			$filters['summonerName']['validators'][] = array(
				'name' => 'FSMPILoL\Validator\PlayerSummonerName',
				'options' => array(
					'object_repository' => $this->getObjectManager()->getRepository(Player::class),
					'tournament' => $this->getTournament(),
					'fields' => 'email'
				)
			);
		}
		
		$filters['isSub'] = array(
			'required' => false,
			'filters' => array(
				array('name' => 'Int'),
			),
			'validators' => array(
				array('name' => 'Between', 'options' => array('min' => 0, 'max' => 2))
			)
		);
		
		return $filters;
	}

	public function getObjectManager() {
		return $this->objectManager;
	}

	public function setObjectManager(ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
		return $this;
	}

	public function getTournament() {
		return $this->tournament;
	}

	public function setTournament(\FSMPILoL\Entity\Tournament $tournament) {
		$this->tournament = $tournament;
	}

}