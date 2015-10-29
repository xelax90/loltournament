<?php

namespace FSMPILoL\Validator;

use FSMPILoL\Validator\NoObjectExistsInTournament;
use Zend\Validator\Exception;
use FSMPILoL\Model\AnmeldungRepository;

/**
 * Description of AnmeldungTournament
 *
 * @author schurix
 */
abstract class AnmeldungTournament extends NoObjectExistsInTournament{
	
	public function __construct(array $options) {
		parent::__construct($options);
		
		if(!$this->objectRepository instanceof AnmeldungRepository){
            throw new Exception\InvalidArgumentException(sprintf(
                'object_repository must be an instance of %s', AnmeldungRepository::class
            ));
		}
	}
	
    /**
     * {@inheritDoc}
     */
    public function isValid($value)
    {
		return false;
    }
	
	
}
