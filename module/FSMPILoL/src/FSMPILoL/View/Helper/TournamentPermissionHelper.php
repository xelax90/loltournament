<?php
namespace FSMPILoL\View\Helper;

use Zend\View\Helper\AbstractHelper;
use FSMPILoL\Service\Tournament\Permission;

/**
 * This helper handles team display permissions for the frontend display
 */
class TournamentPermissionHelper extends AbstractHelper {

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
