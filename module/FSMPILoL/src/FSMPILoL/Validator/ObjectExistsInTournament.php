<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FSMPILoL\Validator;

use DoctrineModule\Validator\ObjectExists;
use Zend\Validator\Exception;
use FSMPILoL\Entity\Tournament;

/**
 * Tests if object exists in tournament
 *
 * @author schurix
 */
class ObjectExistsInTournament extends ObjectExists {
	
	/**
	 * @var Tournament
	 */
	protected $tournament;
	
	public function __construct(array $options) {
        if (!isset($options['tournament']) || !$options['tournament'] instanceof Tournament) {
            throw new Exception\InvalidArgumentException(
                'Key `tournament` must be provided and be an instance of FSMPILoL\Entity\Tournament'
            );
        }
		$this->tournament = $options['tournament'];
		
		parent::__construct($options);
		
	}
	
    public function isValid($value)
    {
        $value = $this->cleanSearchValue($value);
		$value['tournament'] = $this->tournament;
        $match = $this->objectRepository->findBy($value);

        if (is_object($match)) {
            return true;
        }

        $this->error(self::ERROR_NO_OBJECT_FOUND, $value);

        return false;
    }
}
