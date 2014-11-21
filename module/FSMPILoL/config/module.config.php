<?php
namespace FSMPILoL;

return array(
    'controllers' => array(
        'invokables' => array(
            'index' => 'FSMPILoL\Controller\IndexController',
            'tournament' => 'FSMPILoL\Controller\TournamentController',
            'admin' => 'FSMPILoL\Controller\AdminController',
			'tournamentadmin' => 'FSMPILoL\Controller\TournamentAdminController',
			'roundcreator' => 'FSMPILoL\Controller\RoundCreatorController',
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
			'kontakt' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/kontakt',
                    'defaults' => array(
                        'controller' => 'index',
                        'action'     => 'kontakt',
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
			'meldung' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/meldung',
                    'defaults' => array(
                        'controller' => 'Tournament',
                        'action'     => 'meldung',
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
			'myteam' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/myteam',
                    'defaults' => array(
                        'controller' => 'Tournament',
                        'action'     => 'myteam',
                    ),
                ),
            ),
			'zfcadmin' => array(
				'options' => array(
					'defaults' => array(
						'controller' => 'admin',
						'action' => 'index',
					)
				),
				'child_routes' => array(
					'paarungen' => array(
		                'type' => 'segment',
		                'options' => array(
		                    'route'    => '/paarungen[/:match_id]',
		                    'defaults' => array(
		                        'controller' => 'tournamentadmin',
		                        'action'     => 'paarungenAdmin',
								'match_id'   => 0,
		                    ),
		                ),
						'may_terminate' => true,
						'child_routes' => array(
							'block' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/block',
				                    'defaults' => array(
				                        'controller' => 'tournamentadmin',
				                        'action'     => 'paarungBlock',
				                    ),
				                ),
							),
							'unblock' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/unblock',
				                    'defaults' => array(
				                        'controller' => 'tournamentadmin',
				                        'action'     => 'paarungUnblock',
				                    ),
				                ),
							),
							'comment' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/comment',
				                    'defaults' => array(
				                        'controller' => 'tournamentadmin',
				                        'action'     => 'paarungComment',
				                    ),
				                ),
							),
							'setresult' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/setresult',
				                    'defaults' => array(
				                        'controller' => 'tournamentadmin',
				                        'action'     => 'paarungSetResult',
				                    ),
				                ),
							)
						),
		            ),
					'runden' => array(
		                'type' => 'segment',
		                'options' => array(
		                    'route'    => '/rounds[/:group_id]',
		                    'defaults' => array(
		                        'controller' => 'roundcreator',
		                        'action'     => 'index',
								'group_id'   => 0,
		                    ),
							'constraints' => array(
								'group_id'         => '[0-9]*',
							),
		                ),
						'may_terminate' => true,
						'child_routes' => array(
							'create' => array(
				                'type' => 'segment',
				                'options' => array(
				                    'route'    => '/create[/:preset]',
				                    'defaults' => array(
				                        'controller' => 'roundcreator',
				                        'action'     => 'create',
										'preset'     => '',
				                    ),
									'constraints' => array(
										'preset'         => '[a-zA-Z0-9_-]*',
									),
				                ),
							),
							'setpreset' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/setpreset',
				                    'defaults' => array(
				                        'controller' => 'roundcreator',
				                        'action'     => 'setpreset',
				                    ),
				                ),
							),
						),
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
			'FSMPILoL\TeamdataCache' => function (){

				$cache = \Zend\Cache\StorageFactory::factory(array(
				    'adapter' => array(
						'name' => 'FSMPILoL\Cache\Storage\Adapter\Filesystem',
						'options' => array(
							'ttl' => 11200,
							'namespace' => 'teamdata',
							'cache_dir' => './data/cache/',
						),
					),
				    'plugins' => array(
				        'exception_handler' => array('throw_exceptions' => false),
				    ),
				));
				return $cache;
			},
			'FSMPILoL\SummonerdataCache' => function (){

				$cache = \Zend\Cache\StorageFactory::factory(array(
				    'adapter' => array(
						'name' => 'FSMPILoL\Cache\Storage\Adapter\Filesystem',
						'options' => array(
							'ttl' => 11200,
							'namespace' => 'summonerdata',
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
			'FSMPILoL\Options\RoundCreator' => function ($sm) {
                $config = $sm->get('Config');
                return new Options\RoundCreatorOptions(isset($config['fsmpilol_roundcreator']) ? $config['fsmpilol_roundcreator'] : array());
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

	'view_helpers' => array(
		'invokables'=> array(
			'fsmpiLoLDDragon' => 'FSMPILoL\View\Helper\DDragonHelper'
		)
	),
	'navigation' => array(
		'default' => array(
			//array('label' => 'Home', 'route' => 'home'),
			array('label' => 'Info', 'route' => 'info'),
			array('label' => 'Tabelle', 'route' => 'ergebnisse'),
			array('label' => 'Paarungen', 'route' => 'paarungen'),
			array('label' => 'Teilnehmer', 'route' => 'teams'),
			array('label' => 'Kontakt', 'route' => 'kontakt'),
		),
		'admin' => array(
			array('label' => 'Paarungen', 'route' => 'zfcadmin/paarungen'),
			array('label' => 'Runden', 'route' => 'zfcadmin/runden'),
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
	
	'fsmpilol_roundcreator' => array(
		'round_types' => array(
			'swiss' => 'FSMPILoL\Tournament\RoundCreator\SwissRoundCreator',
			'random' => 'FSMPILoL\Tournament\RoundCreator\RandomRoundCreator',
			'knockout' => 'FSMPILoL\Tournament\RoundCreator\KnockoutRoundCreator',
		),
	),
);
