<?php
namespace FSMPILoL;

return array(
    'controllers' => array(
        'invokables' => array(
            'index' => 'FSMPILoL\Controller\IndexController',
            'tournament' => 'FSMPILoL\Controller\TournamentController'
        ),
    ),
    
    
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'index',
                        'action'     => 'index',
                    ),
                ),
            ),
			'info' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/info',
                    'defaults' => array(
                        'controller' => 'index',
                        'action'     => 'info',
                    ),
                ),
            ),
			'ergebnisse' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/ergebnisse',
                    'defaults' => array(
                        'controller' => 'Tournament',
                        'action'     => 'ergebnisse',
                    ),
                ),
            ),
			'paarungen' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/paarungen',
                    'defaults' => array(
                        'controller' => 'Tournament',
                        'action'     => 'paarungen',
                    ),
                ),
            ),
			'teams' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/teams',
                    'defaults' => array(
                        'controller' => 'Tournament',
                        'action'     => 'teams',
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
							'ttl' => 11200,
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
			'FSMPILoL\Options\API' => function ($sm) {
                $config = $sm->get('Config');
                return new Options\APIOptions(isset($config['fsmpilol_api']) ? $config['fsmpilol_api'] : array());
            },
			'FSMPILoL\Options\Anmeldung' => function ($sm) {
                $config = $sm->get('Config');
                return new Options\AnmeldungOptions(isset($config['fsmpilol_anmeldung']) ? $config['fsmpilol_anmeldung'] : array());
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
			array('label' => 'Info', 'route' => 'info'),
			array('label' => 'Tabelle', 'route' => 'ergebnisse'),
			array('label' => 'Paarungen', 'route' => 'paarungen'),
			array('label' => 'Teilnehmer', 'route' => 'teams'),
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
