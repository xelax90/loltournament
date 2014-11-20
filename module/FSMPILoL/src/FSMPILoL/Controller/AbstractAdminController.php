<?php
namespace FSMPILoL\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use FSMPILoL\Entity\User;
use Zend\Mvc\MvcEvent;

abstract class AbstractAdminController extends AbstractActionController
{
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * @return Doctrine\ORM\EntityManager
	 */
	public function getEntityManager(){
		if (null === $this->em) {
			$this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->em;
	}
	
	/**
	 * @param MvcEvent $e
	 */
	public function onDispatch(MvcEvent $e) {
		if(!$this->authenticate()){
			return $this->redirect()->toRoute('home');
		}
		parent::onDispatch($e);
	}
	
	/**
	 * Authenticate user and check if he has moderator rights
	 * @return boolean
	 */
	protected function authenticate(){
		if($this->zfcUserAuthentication()->hasIdentity()){
			return $this->zfcUserAuthentication()->getIdentity()->getRole() <= User::ROLE_MODERATOR;
		}
		
        $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        $adapter->prepareForAuthentication($this->getRequest());
        $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);
		
		if($this->zfcUserAuthentication()->hasIdentity()){
			return $this->zfcUserAuthentication()->getIdentity()->getRole() <= User::ROLE_MODERATOR;
		}
		return false;
	}

}
