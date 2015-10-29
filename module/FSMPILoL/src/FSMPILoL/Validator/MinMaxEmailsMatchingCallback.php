<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Validator;

use Zend\Validator\AbstractValidator;

/**
 * Description of MinMaxEmailsMatchingCallback
 *
 * @author schurix
 */
class MinMaxEmailsMatchingCallback extends AbstractValidator{
	const MESSAGE_NOT_ENOUGH = 'notEnough';
	const MESSAGE_TOO_MANY = 'tooMany';
	
	protected $messageTemplates = array(
		self::MESSAGE_NOT_ENOUGH => 'Nicht genügend passende E-Mail Adressen gefunden',
		self::MESSAGE_TOO_MANY => 'Zu viele passende E-Mail Adressen gefunden',
	);
	
	/**
	 * Options for the between validator
	 *
	 * @var array
	 */
	protected $options = array(
		'ignore_empty' => true, // Whether to ignore empty e-mails or count them according to callback return
		'anmeldung_key' => 'anmeldungen', // key of anmeldung fieldset. TODO: use path to email instead of key to anmeldung
		'callback' => false,
		'min'       => 0,
		'max'       => PHP_INT_MAX,
	);
	
	
	public function isValid($value, $context = null) {
		if(!$context){
			return true;
		}
		
		$anmeldungenKey = $this->getOption('anmeldung_key');
		$callback = $this->getCallback();
		$ignoreEmpty = !!$this->getOption('ignore_empty');
		$min = $this->getOption('min');
		$max = $this->getOption('max');
		
		if(!is_callable($callback)){
			throw new \Zend\Validator\Exception\InvalidArgumentException('No valid callback provided');
		}
		
		// TODO provide path to all email fields instead of anmldung
		if(!empty($anmeldungenKey) && !isset($context[$anmeldungenKey])){
			throw new \Zend\Validator\Exception\InvalidArgumentException(sprintf('Anmeldungen key "%s" not set', $anmeldungenKey));
		}
		
		$positives = 0;
		$negatives = 0;
		$notEmpty = 0;
		
		if(empty($anmeldungenKey)){
			// allow empty key for single fieldset validation
			$anmeldungen = array($context);
		} else {
			$anmeldungen = $context[$anmeldungenKey];
		}
		foreach($anmeldungen as $k => $anmeldung){
			$isMatching = call_user_func($callback, $anmeldung['email']);
			$isEmpty = empty($anmeldung['email']) && $ignoreEmpty;
			if($isMatching){
				if(!$isEmpty){
					$positives++;
				}
			} elseif(!$isEmpty){
				$negatives++;
			}
			if(!$isEmpty){
				$notEmpty++;
			}
		}
		
		if(is_int($min) && $positives < $min){
			$this->error(static::MESSAGE_NOT_ENOUGH);
			return false;
		} elseif(is_float($min) && $positives/$notEmpty < $min){
			$this->error(static::MESSAGE_NOT_ENOUGH);
			return false;
		}
		
		if(is_int($max) && $positives > $max){
			$this->error(static::MESSAGE_TOO_MANY);
			return false;
		} elseif(is_float($max) && $positives/$notEmpty > $max){
			$this->error(static::MESSAGE_TOO_MANY);
			return false;
		}
		
		return true;
	}
	
	protected function getCallback(){
		$callback = $this->getOption('callback');
		return $callback;
	}
}
