<?php

namespace FSMPILoL\Validator;

use FSMPILoL\Validator\NoObjectExistsInTournament;
use Zend\Validator\Exception;
use FSMPILoL\Model\PlayerRepository;

/**
 * Description of PlayerSummonerName
 *
 * @author schurix
 */
abstract class PlayerTournament extends NoObjectExistsInTournament{
	
	public function __construct(array $options) {
		parent::__construct($options);
		
		if(!$this->objectRepository instanceof PlayerRepository){
            throw new Exception\InvalidArgumentException(
                'object_repository must be an instance of FSMPILoL\Module\PlayerRepository'
            );
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
