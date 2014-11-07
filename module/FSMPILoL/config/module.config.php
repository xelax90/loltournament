<?php
namespace FSMPILoL;

return array(
    'controllers' => array(
        'invokables' => array(
            'index' => 'FSMPILoL\Controller\IndexController'
        ),
    ),
    
    
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'index',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
		'factories' => array(
			'FSMPILoL\Log' => function ($sm) {
				$log = new Zend\Log\Logger();
				$writer = new Zend\Log\Writer\Stream('./data/logs/fsmpilol.log');
				$log->addWriter($writer);
				return $log;
			},
			'FSMPILoL\RiotCache' => function (){

				$cache = \Zend\Cache\StorageFactory::factory(array(
				    'adapter' => array(
						'name' => 'FSMPILoL\Cache\Storage\Adapter\Filesystem',
						'options' => array(
							'ttl' => 60,
							'namespace' => 'riotcache',
							'cache_dir' => './data/cache/',
						),
					),
				    'plugins' => array(
				        'exception_handler' => array('throw_exceptions' => false),
				    ),
				));
				return $cache;
			},
			'riotapi_config' => function ($sm) {
                $config = $sm->get('Config');
                return new Options\APIOptions(isset($config['fsmpilol_api']) ? $config['fsmpilol_api'] : array());
            },
		),
    ),
    
    'translator' => array(
        'locale' => 'de_DE',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/fsmpi-lo-l/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    
	'navigation' => array(
		'default' => array(
			array('label' => 'Home', 'route' => 'home'),
			array('label' => 'Home', 'route' => 'home'),
			array('label' => 'Home', 'route' => 'home'),
			array('label' => 'Home', 'route' => 'home'),
			array('label' => 'Home', 'route' => 'home'),
		),
	),
    
    
	'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),


	'doctrine' => array(
		'driver' => array(
			__NAMESPACE__ . '_driver' => array(
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
			),
			'orm_default' => array(
				'drivers' => array(
					__NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
				)
			)
		)
	),

);
