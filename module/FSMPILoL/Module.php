<?php
namespace FSMPILoL;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\EventManager\EventManager;
use Zend\View\Helper\Navigation;

class Module
{
    public function onBootstrap(MvcEvent $e){
		$app = $e->getApplication();
		$eventManager = $app->getEventManager();
		$sm = $app->getServiceManager();
		
		// Attach UserListener for role and UserProfile handling
		$listener = $sm->get('FSMPILoL\UserListener');
		$eventManager->attach($listener);
		
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		
		if(!\Zend\Console\Console::isConsole()) {
			// Add ACL information to the Navigation view helper
			$authorize = $sm->get('BjyAuthorizeServiceAuthorize');
			$acl = $authorize->getAcl();
			$role = $authorize->getIdentity();
			Navigation::setDefaultAcl($acl);
			Navigation::setDefaultRole($role);		
		}

		// Add UTF8 handler to EntityManager
	    $em = $sm->get('doctrine.entitymanager.orm_default');
		$em->getEventManager()->addEventSubscriber( new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit('utf8', 'utf8_general_ci') );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

	public function getServiceConfig(){
		return array(
            'invokables' => array(
            ),
			'factories' => array(
				'Navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
				'StreamNavigation' => 'Zend\Navigation\Service\StreamNavigationFactory',
			),
		);
	}
}
