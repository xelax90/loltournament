<?php
namespace FSMPILoL\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	public function getEntityManager(){
		if (null === $this->em) {
			$this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->em;
	}
	
	public function indexAction(){
		//$api = new RiotAPI($this->getServiceLocator());
		return new ViewModel();
	}
	
	public function infoAction(){
		return new ViewModel();
	}
	
	public function kontaktAction(){
		return new ViewModel();
	}
}
