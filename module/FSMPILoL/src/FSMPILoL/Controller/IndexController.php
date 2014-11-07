<?php
namespace FSMPILoL\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use FSMPILoL\Riot\RiotAPI;

class IndexController extends AbstractActionController
{
	public function getEntityManager(){
		if (null === $this->em) {
			$this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->em;
	}
	
    public function indexAction()
    {
		$api = new RiotAPI($this->getServiceLocator());
        return new ViewModel();
    }
}
