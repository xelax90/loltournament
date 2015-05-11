<?php

namespace FSMPILoL\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use FSMPILoL\Entity\Role;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

class LoadUserRoles extends AbstractFixture implements FixtureInterface, ServiceLocatorAwareInterface
{
	/**
	 *
	 * @var ServiceLocatorInterface
	 */
	protected $sl;
	
	/**
	 *
	 * @var \FSMPILoL\Options\SkelletonOptions
	 */
	protected $skelletonOptions;
	
    /**
     * @return \SkelletonApplication\Options\SkelletonOptions
     */
    public function getSkelletonOptions()
    {
        if (!$this->skelletonOptions instanceof \FSMPILoL\Options\SkelletonOptions) {
            $this->skelletonOptions = $this->getServiceLocator()->get('SkelletionApplication\Options\Application');
        }
        return $this->skelletonOptions;
    }
	
	
    public function load(ObjectManager $manager)
    {
		$skelletonOptions = $this->getSkelletonOptions();
		$roles = $skelletonOptions->getRoles();
		
		$this->saveRoles($manager, $roles);
        $manager->flush();
    }
	
	protected function saveRoles(ObjectManager $manager, $roles, $parent = null){
		foreach($roles as $roleName => $children){
			$role = new Role();
			$role->setRoleId($roleName);
			$role->setParent($parent);
			$manager->persist($role);
			$this->saveRoles($manager, $children, $role);
			
			if(empty($children) && strpos(strtolower($roleName), 'admin') !== false){
				$this->addReference('admin-role', $role);
			}
		}
	}
	
	/**
	 * Returns ServiceLocator
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator() {
		return $this->sl;
	}
	
	/**
	 * Sets ServiceLocator
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->sl = $serviceLocator;
	}

}