<?php
namespace FSMPILoL\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use FSMPILoL\Service\Tournament\Permission;

/**
 * Tournament Permission handling plugin
 */
class TournamentPermissionPlugin extends AbstractPlugin{
	
	/**
	 * @var Permission
	 */
	protected $permission;
	
	public function __construct(){
		
	}
	
	public function isAllowed($ressource, $team = null) {
		return $this->getPermission()->isAllowed($ressource, $team);
	}

	public function setPermission(Permission $permission) {
		$this->permission = $permission;
	}
	/**
	 * @return Permission;
	 */
	protected function getPermission(){
		return $this->permission;
	}
	
}
