<?php
namespace FSMPILoL\Controller;

use Zend\View\Model\ViewModel;
class AdminController extends AbstractAdminController
{
	public function indexAction() {
		$view = new ViewModel();
		$view->setTemplate('zfc-admin/admin/index');
		return $view;
	}
}
