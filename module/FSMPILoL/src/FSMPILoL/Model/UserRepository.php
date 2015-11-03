<?php
namespace FSMPILoL\Model;

use SkelletonApplication\Model\UserRepository as SkelletonRepository;

/**
 * Provides getAdminUsers functionality
 *
 * @author schurix
 */
class UserRepository extends SkelletonRepository{
	public function getAdminUsers(){
		$query = $this->createQueryBuilder('u')
			->join('u.roles', 'r')
			->andWhere('r.roleId = ?1')
			->setParameter(1, 'administrator')
			->orderBy('u.displayName', 'ASC');
		return $query->getQuery()->getResult();
	}
}
