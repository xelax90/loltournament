<?php
namespace FSMPILoL\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\MvcEvent;

abstract class AbstractAdminController extends AbstractActionController
{
	/** @var EntityManager */
	protected $em;
	
	/**
	 * @return EntityManager
	 */
	public function getEntityManager(){
		if (null === $this->em) {
			$this->em = $this->getServiceLocator()->get(EntityManager::class);
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
		if(!$this->zfcUserAuthentication()->hasIdentity()){
			$adapter = $this->zfcUserAuthentication()->getAuthAdapter();
			$adapter->prepareForAuthentication($this->getRequest());
			$this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);
		}
		
		if(!$this->zfcUserAuthentication()->hasIdentity()){
			$this->redirect()->toRoute('zfcuser/login');
			return false;
		}
		
		return true;
	}

}
