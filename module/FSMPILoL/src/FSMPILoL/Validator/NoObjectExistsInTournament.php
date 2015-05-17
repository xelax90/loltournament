<?php

namespace FSMPILoL\Validator;

/**
 * Class that validates if objects does not exist in a given repository with a given list of matched fields
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @since   0.4.0
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class NoObjectExistsInTournament extends ObjectExistsInTournament
{
    /**
     * Error constants
     */
    const ERROR_OBJECT_FOUND    = 'objectFound';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = array(
        self::ERROR_OBJECT_FOUND    => "An object matching '%value%' was found",
    );

    /**
     * {@inheritDoc}
     */
    public function isValid($value)
    {
        $value = $this->cleanSearchValue($value);
		$value['tournament'] = $this->tournament;
        $match = $this->objectRepository->findOneBy($value);

        if (is_object($match)) {
            $this->error(self::ERROR_OBJECT_FOUND, $value['email']);

            return false;
        }

        return true;
    }
}
