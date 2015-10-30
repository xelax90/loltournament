<?php

/* 
 * Copyright (C) 2014 schurix
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace FSMPILoL\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use SkelletonApplication\Entity\User;
use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use SkelletonApplication\Fixtures\LoadUserRoles;

class LoadUsers extends AbstractFixture implements FixtureInterface, ServiceLocatorAwareInterface, DependentFixtureInterface
{
	/**
	 *
	 * @var ServiceLocatorInterface
	 */
	protected $sl;
	
	/**
	 *
	 * @var \ZfcUser\Options\ModuleOptions
	 */
	protected $zfcUserOptions;
	
    /**
     * @return \ZfcUser\Options\ModuleOptions
     */
    public function getZfcUserOptions()
    {
        if (!$this->zfcUserOptions instanceof ZfcUserModuleOptions) {
            $this->zfcUserOptions = $this->getServiceLocator()->get('zfcuser_module_options');
        }
        return $this->zfcUserOptions;
    }
	
	
    public function load(ObjectManager $manager)
    {
		$users = array(
			array(
				'id' => 1,
				'email' => 'a@rwth-aachen.de',
				'name' => 'Alex Grinspunn',
				'password' => 'aleksandr'
			),
			array(
				'id' => 2,
				'email' => 'f@rwth-aachen.de',
				'name' => 'Florian Großkreuz',
				'password' => 'florian'
			),
			array(
				'id' => 3,
				'email' => 'e@rwth-aachen.de',
				'name' => 'Eiko Kerinnis',
				'password' => 'eiko'
			),
			array(
				'id' => 4,
				'email' => 'l@rwth-aachen.de',
				'name' => 'Lukas Prediger',
				'password' => 'lukas'
			),
			array(
				'id' => 5,
				'email' => 'dm@rwth-aachen.de',
				'name' => 'Dennis Matz',
				'password' => 'dennis'
			),
			array(
				'id' => 6,
				'email' => 'm@rwth-aachen.de',
				'name' => 'Markus Brieden',
				'password' => 'markus'
			),
			array(
				'id' => 7,
				'email' => 'dk@rwth-aachen.de',
				'name' => 'Dennis Klingelnberg',
				'password' => 'dennis'
			),
		);
		
		$role = $this->getReference('admin-role');
		
		
		/* @var $userService \ZfcUser\Service\User */
		$userService = $this->getServiceLocator()->get('zfcuser_user_service');
		
        $zfcUserOptions = $this->getZfcUserOptions();
		foreach($users as $user){
			$found = $userService->getUserMapper()->findByEmail($user['email']);
			if($found){
				continue;
			}
			
			
			$u = new User();
			$u->setId($user['id']);
			$u->setUsername($user['email']);
			$u->setEmail($user['email']);
			$u->setDisplayName($user['name']);
			$u->setUsername(null);
			$u->addRole($role);
			$u->setState(1);

			$bcrypt = new Bcrypt;
			$bcrypt->setCost($zfcUserOptions->getPasswordCost());
			$u->setPassword($bcrypt->create($user['password']));

			$manager->persist($u);
		}
        $manager->flush();
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

	public function getDependencies() {
		return array(LoadUserRoles::class);
	}

}