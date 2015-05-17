<?php
namespace FSMPILoL;

use Zend\ServiceManager\AbstractPluginManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;

$xelaxConfig = array(
	/*
	 * Configure your list controllers. Routes are generated automatically, but
	 * you can also create your own routes. Route names must correspond with 
	 * controller names.
	 */
	'list_controller' => array(
		'player' => array(
			'name' => 'Player',
			'controller_class' => 'FSMPILoL\Controller\PlayerController', 
			'base_namespace' => 'FSMPILoL',
			'list_columns' => array('Id' => 'id', 'Name' => 'name', 'Summoner Name' => 'summonerName', 'EMail' => 'email'),
			'route_base' => 'zfcadmin/teams/player', 
			'rest_enabled' => false,
			'list_route' => array(
				//'route' => 'zfcadmin/teams'
			),
		),
		'myPlayer' => array(
			'name' => 'Player',
			'controller_class' => 'FSMPILoL\Controller\PlayerController', 
			'base_namespace' => 'FSMPILoL',
			'list_columns' => array('Id' => 'id', 'Name' => 'name', 'Summoner Name' => 'summonerName', 'EMail' => 'email'),
			'route_base' => 'zfcadmin/myteams/player', 
			'rest_enabled' => false,
			'list_route' => array(
				//'route' => 'zfcadmin/teams'
			),
		),
	),
);

return array(
    'controllers' => array(
        'invokables' => array(
            'index' => 'FSMPILoL\Controller\IndexController',
            'tournament' => 'FSMPILoL\Controller\TournamentController',
            'admin' => 'FSMPILoL\Controller\AdminController',
			'tournamentadmin' => 'FSMPILoL\Controller\TournamentAdminController',
			'roundcreator' => 'FSMPILoL\Controller\RoundCreatorController',
			'teamadmin' => 'FSMPILoL\Controller\TeamAdminController',
			'myteamadmin' => 'FSMPILoL\Controller\MyTeamAdminController',
			'anmeldung' => 'FSMPILoL\Controller\AnmeldungController',
			'FSMPILoL\Controller\PlayerController' => 'FSMPILoL\Controller\PlayerController',
        ),
    ),
    
    
    'xelax' => $xelaxConfig,
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
            'anmeldung' => array(
                'type' => 'literal',
                'options' => array(
                    'route'    => '/anmeldung',
                    'defaults' => array(
                        'controller' => 'anmeldung',
                        'action'     => 'index',
                    ),
                ),
				'may_terminate' => false,
				'child_routes' => array(
					'form' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/form',
							'defaults' => array(
								'controller' => 'anmeldung',
								'action'     => 'form',
							),
						),

					)
				)
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
							'edit' => array(
				                'type' => 'segment',
				                'options' => array(
				                    'route'    => '/edit/:round_id',
									'constraints' => array(
										'round_id'    => '[0-9]+',
									),
				                ),
								'may_terminate' => false,
								'child_routes' => array(
									'hide' => array(
										'type' => 'literal',
										'options' => array(
											'route'    => '/hide',
											'defaults' => array(
												'controller' => 'roundcreator',
												'action'     => 'hide',
											),
											'constraints' => array(
												'round_id'    => '[0-9]+',
											),
										),
									),
									'show' => array(
										'type' => 'literal',
										'options' => array(
											'route'    => '/show',
											'defaults' => array(
												'controller' => 'roundcreator',
												'action'     => 'show',
											),
										),
									),
									'delete' => array(
										'type' => 'literal',
										'options' => array(
											'route'    => '/delete',
											'defaults' => array(
												'controller' => 'roundcreator',
												'action'     => 'delete',
											),
										),
									),
								),
							),
						),
					),
					'teams' => array(
		                'type' => 'segment',
		                'options' => array(
		                    'route'    => '/teams[/:team_id]',
		                    'defaults' => array(
		                        'controller' => 'teamadmin',
		                        'action'     => 'index',
								'team_id'   => 0,
		                    ),
							'constraints' => array(
								'team_id'         => '[0-9]*',
							),
		                ),
						'may_terminate' => true,
						'child_routes' => array(
							'anmerkung' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/anmerkung',
									'defaults' => array(
										'controller' => 'teamadmin',
										'action' => 'anmerkung',
									)
				                ),
							),
							'block' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/block',
									'defaults' => array(
										'controller' => 'teamadmin',
										'action' => 'block',
									)
				                ),
							),
							'unblock' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/unblock',
									'defaults' => array(
										'controller' => 'teamadmin',
										'action' => 'unblock',
									)
				                ),
							),
							'warn' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/warn',
									'defaults' => array(
										'controller' => 'teamadmin',
										'action' => 'warn',
									)
				                ),
							),
							'warnPlayer' => array(
				                'type' => 'segment',
				                'options' => array(
				                    'route'    => '/warnPlayer/:player_id',
									'defaults' => array(
										'controller' => 'teamadmin',
										'action' => 'warnPlayer',
										'player_id' => 0,
									),
									'constraints' => array(
										'player_id'         => '[0-9]*',
									),
				                ),
							),
							'deleteWarning' => array(
				                'type' => 'segment',
				                'options' => array(
				                    'route'    => '/deleteWarning/:warning_id',
									'defaults' => array(
										'controller' => 'teamadmin',
										'action' => 'deleteWarning',
										'warning_id' => 0,
									),
									'constraints' => array(
										'warning_id'         => '[0-9]*',
									),
				                ),
							),
							'player' => array(
								'type' => 'XelaxAdmin\Router\ListRoute',
								'options' => array(
									// the config key of the options
									'controller_options_name' => 'player',
								),
							),
							'addsub' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/addsub',
									'defaults' => array(
										'controller' => 'teamadmin',
										'action' => 'addsub',
									)
				                ),
							),
							'makesub' => array(
				                'type' => 'segment',
				                'options' => array(
				                    'route'    => '/makesub/:player_id',
									'defaults' => array(
										'controller' => 'teamadmin',
										'action' => 'makesub',
										'player_id' => 0,
									),
									'constraints' => array(
										'player_id'         => '[0-9]*',
									),
				                ),
							),
						),
					),
					'myteams' => array(
		                'type' => 'segment',
		                'options' => array(
		                    'route'    => '/myteams[/:team_id]',
		                    'defaults' => array(
		                        'controller' => 'myteamadmin',
		                        'action'     => 'index',
								'team_id'   => 0,
		                    ),
							'constraints' => array(
								'team_id'         => '[0-9]*',
							),
		                ),
						'may_terminate' => true,
						'child_routes' => array(
							'anmerkung' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/anmerkung',
									'defaults' => array(
										'controller' => 'myteamadmin',
										'action' => 'anmerkung',
									)
				                ),
							),
							'block' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/block',
									'defaults' => array(
										'controller' => 'myteamadmin',
										'action' => 'block',
									)
				                ),
							),
							'unblock' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/unblock',
									'defaults' => array(
										'controller' => 'myteamadmin',
										'action' => 'unblock',
									)
				                ),
							),
							'warn' => array(
				                'type' => 'literal',
				                'options' => array(
				                    'route'    => '/warn',
									'defaults' => array(
										'controller' => 'teamadmin',
										'action' => 'warn',
									)
				                ),
							),
							'warnPlayer' => array(
				                'type' => 'segment',
				                'options' => array(
				                    'route'    => '/warnPlayer/:player_id',
									'defaults' => array(
										'controller' => 'teamadmin',
										'action' => 'warnPlayer',
										'player_id' => 0,
									),
									'constraints' => array(
										'player_id'         => '[0-9]*',
									),
				                ),
							),
							'deleteWarning' => array(
				                'type' => 'segment',
				                'options' => array(
				                    'route'    => '/deleteWarning/:warning_id',
									'defaults' => array(
										'controller' => 'teamadmin',
										'action' => 'deleteWarning',
										'warning_id' => 0,
									),
									'constraints' => array(
										'warning_id'         => '[0-9]*',
									),
				                ),
							),
							'player' => array(
								'type' => 'XelaxAdmin\Router\ListRoute',
								'options' => array(
									// the config key of the options
									'controller_options_name' => 'myPlayer',
								),
							),
						),
					),
				),
			),
        ),
    ),
    
	'bjyauthorize' => array(
		// resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => array(
            "BjyAuthorize\Provider\Resource\Config" => array(
                'user' => array(),
				'tournament' => array(),
            ),
        ),

		
		'rule_providers' => array(
			"BjyAuthorize\Provider\Rule\Config" => array(
                'allow' => array(
					// config for navigation
                    [['user'],  'user', 'profile'],
                    [['user'],  'user', 'logout'],
                    [['user'],  'user', 'changepassword'],
                    [['guest'], 'user', 'login'],
                    [['guest'], 'user', 'register'],
                    [['moderator'],     'tournament', 'round/viewHidden'],
                    [['moderator'],     'tournament', 'debug/moderator'],
                    [['administrator'], 'tournament', 'debug/administrator'],
                ),

                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny' => array(
                    // ...
                ),
            )
		),
		
		'guards' => array(
			'BjyAuthorize\Guard\Route' => array(
				// user
				['route' => 'zfcuser',                  'roles' => ['guest', 'user'] ],
				['route' => 'zfcuser/login',            'roles' => ['guest', 'user'] ],
				['route' => 'zfcuser/register',         'roles' => [] ],
				['route' => 'zfcuser/authenticate',     'roles' => ['guest'] ],
				['route' => 'zfcuser/logout',           'roles' => ['guest', 'user'] ],
				['route' => 'zfcuser/changepassword',   'roles' => ['user'] ],
				['route' => 'zfcuser/changeemail',      'roles' => ['user'] ],
				
				// webpage
				['route' => 'home',                     'roles' => ['guest', 'user'] ],
				['route' => 'info',                     'roles' => ['guest', 'user'] ],
				['route' => 'kontakt',                  'roles' => ['guest', 'user'] ],
				['route' => 'ergebnisse',               'roles' => ['guest', 'user'] ],
				['route' => 'paarungen',                'roles' => ['guest', 'user'] ],
				['route' => 'meldung',                  'roles' => ['user', 'guest'] ],
				['route' => 'teams',                    'roles' => ['guest', 'user'] ],
				['route' => 'myteam',                   'roles' => ['user', 'guest'] ],
				['route' => 'anmeldung',                'roles' => ['guest', 'user'] ],
				['route' => 'anmeldung/form',           'roles' => ['guest', 'user'] ],
				
				// modules
				['route' => 'doctrine_orm_module_yuml', 'roles' => ['administrator'] ],
				
				// admin
				['route' => 'zfcadmin',                      'roles' => ['moderator']],
				// user admin
				['route' => 'zfcadmin/zfcuseradmin',         'roles' => ['administrator']],
				['route' => 'zfcadmin/zfcuseradmin/list',    'roles' => ['administrator']],
				['route' => 'zfcadmin/zfcuseradmin/create',  'roles' => ['administrator']],
				['route' => 'zfcadmin/zfcuseradmin/edit',    'roles' => ['administrator']],
				['route' => 'zfcadmin/zfcuseradmin/remove',  'roles' => ['administrator']],
				// paarung
				['route' => 'zfcadmin/paarungen',            'roles' => ['moderator']],
				['route' => 'zfcadmin/paarungen/block',      'roles' => ['moderator']],
				['route' => 'zfcadmin/paarungen/unblock',    'roles' => ['moderator']],
				['route' => 'zfcadmin/paarungen/comment',    'roles' => ['moderator']],
				['route' => 'zfcadmin/paarungen/setresult',  'roles' => ['moderator']],
				// runden
				['route' => 'zfcadmin/runden',               'roles' => ['moderator']],
				['route' => 'zfcadmin/runden/create',        'roles' => ['administrator']],
				['route' => 'zfcadmin/runden/setpreset',     'roles' => ['administrator']],
				['route' => 'zfcadmin/runden/edit',          'roles' => ['administrator']],
				// teams
				['route' => 'zfcadmin/teams',                'roles' => ['moderator']],
				['route' => 'zfcadmin/teams/anmerkung',      'roles' => ['moderator']],
				['route' => 'zfcadmin/teams/block',          'roles' => ['moderator']],
				['route' => 'zfcadmin/teams/unblock',        'roles' => ['moderator']],
				['route' => 'zfcadmin/teams/warn',           'roles' => ['moderator']],
				['route' => 'zfcadmin/teams/warnPlayer',     'roles' => ['moderator']],
				['route' => 'zfcadmin/teams/deleteWarning',  'roles' => ['moderator']],
				['route' => 'zfcadmin/teams/player',         'roles' => ['moderator']],
				['route' => 'zfcadmin/teams/addsub',         'roles' => ['moderator']],
				['route' => 'zfcadmin/teams/makesub',        'roles' => ['moderator']],
				// myteams
				['route' => 'zfcadmin/myteams',              'roles' => ['moderator']],
				['route' => 'zfcadmin/myteams/block',        'roles' => ['moderator']],
				['route' => 'zfcadmin/myteams/unblock',      'roles' => ['moderator']],
				['route' => 'zfcadmin/myteams/warn',         'roles' => ['moderator']],
				['route' => 'zfcadmin/myteams/warnPlayer',   'roles' => ['moderator']],
				['route' => 'zfcadmin/myteams/deleteWarning','roles' => ['moderator']],
				['route' => 'zfcadmin/myteams/anmerkung',    'roles' => ['moderator']],
				['route' => 'zfcadmin/myteams/player',       'roles' => ['moderator']],
			)
		)
		
	),
	
	'skelleton_application' => array(
		'roles' => array(
			'guest' => array(),
			'user' => array(
				'moderator' => array(
					'administrator' => array() // Admin role must be leaf and must contain 'admin'
				)
			)
		)
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
			'FSMPILoL\Tournament\Permission' => function($sm){
				return new Service\Tournament\Permission();
			},
			'SkelletionApplication\Options\Application' => function (\Zend\ServiceManager\ServiceManager $sm) {
                $config = $sm->get('Config');
                return new Options\SkelletonOptions(isset($config['skelleton_application']) ? $config['skelleton_application'] : array());
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
					
	'controller_plugins' => array(
		'factories' => array(
			'fsmpiLoLTournamentPermission' => function (AbstractPluginManager $pluginManager) {
				$serviceLocator = $pluginManager->getServiceLocator();
				$helper = new Controller\Plugin\TournamentPermissionPlugin();
				$permission = $serviceLocator->get('FSMPILoL\Tournament\Permission');
				$helper->setPermission($permission);
				return $helper;
			},
		),
	),
	'view_helpers' => array(
		'invokables'=> array(
			'fsmpiLoLDDragon' => 'FSMPILoL\View\Helper\DDragonHelper',
		), 
		'factories' => array(
			'fsmpiLoLTournamentPermission' => function (AbstractPluginManager $pluginManager) {
				$serviceLocator = $pluginManager->getServiceLocator();
				$helper = new View\Helper\TournamentPermissionHelper();
				$permission = $serviceLocator->get('FSMPILoL\Tournament\Permission');
				$helper->setPermission($permission);
				return $helper;
			}
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
			array('label' => 'Teams', 'route' => 'zfcadmin/teams'),
			array('label' => 'Meine Teams', 'route' => 'zfcadmin/myteams'),
		),
		'streaming' => array(
			
		)
	),
	
	'form_elements' => array(
		'initializers' => array(
			'ObjectManagerInitializer' => function ($element, $formElements) {
				if ($element instanceof ObjectManagerAwareInterface) {
					$services      = $formElements->getServiceLocator();
					$entityManager = $services->get('Doctrine\ORM\EntityManager');
					$element->setObjectManager($entityManager);
				}
			},
			'TournamentInitializer' => function($element, $formElements){
				if($element instanceof Tournament\TournamentAwareInterface){
					$services      = $formElements->getServiceLocator();
					$options       = $services->get('FSMPILoL\Options\Anmeldung');
					$tournamentId  = $options->getTournamentId();
					$em            = $services->get('Doctrine\ORM\EntityManager');
					$tournament    = $em->getRepository('FSMPILoL\Entity\Tournament')->find($tournamentId);
					$element->setTournament($tournament);
				}
			}
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
		),
		
		// Fixtures to create admin user and default roles
		'fixture' => array(
			'FSMPILoL_fixture' => __DIR__ . '/../data/Fixtures',
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
