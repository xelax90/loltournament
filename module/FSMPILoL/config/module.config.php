<?php
namespace FSMPILoL;

use Zend\ServiceManager\AbstractPluginManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use XelaxAdmin\Controller\ListController;
use BjyAuthorize\Provider;
use BjyAuthorize\Guard;

$xelaxConfig = array(
	/*
	 * Configure your list controllers. Routes are generated automatically, but
	 * you can also create your own routes. Route names must correspond with 
	 * controller names.
	 */
	'list_controller' => array(
		'player' => array(
			'name' => 'Player',
			'controller_class' => Controller\PlayerController::class, 
			'base_namespace' => 'FSMPILoL',
			'list_columns' => array(gettext_noop('Id') => 'id', gettext_noop('Name') => 'name', gettext_noop('Summoner Name') => 'summonerName', gettext_noop('EMail') => 'email'),
			'route_base' => 'zfcadmin/teams/player', 
			'rest_enabled' => false,
		),
		'myPlayer' => array(
			'name' => 'Player',
			'controller_class' => Controller\PlayerController::class, 
			'base_namespace' => 'FSMPILoL',
			'list_columns' => array(gettext_noop('Id') => 'id', gettext_noop('Name') => 'name', gettext_noop('Summoner Name') => 'summonerName', gettext_noop('EMail') => 'email'),
			'route_base' => 'zfcadmin/myteams/player', 
			'rest_enabled' => false,
		),
		'tournament' => array(
			'name' => gettext_noop('Tournament'),
			'controller_class' => ListController::class, 
			'base_namespace' => 'FSMPILoL',
			'list_columns' => array(gettext_noop('Id') => 'id', gettext_noop('Name') => 'name'),
			'list_title' => gettext_noop('Tournaments'),
			'route_base' => 'zfcadmin/tournament',
			'rest_enabled' => false,
		),
	),
);

$guardConfig = array(
	// webpage
	['route' => 'home',                     'roles' => ['guest', 'user'] ],
	['route' => 'info',                     'roles' => ['guest', 'user'] ],
	['route' => 'kontakt',                  'roles' => ['guest', 'user'] ],
	['route' => 'ergebnisse',               'roles' => ['guest', 'user'] ],
	['route' => 'paarungen',                'roles' => ['guest', 'user'] ],
	['route' => 'meldung',                  'roles' => ['user', 'guest'] ],
	['route' => 'teams',                    'roles' => ['guest', 'user'] ],
	['route' => 'teilnehmer',               'roles' => ['guest', 'user'] ],
	['route' => 'myteam',                   'roles' => ['user', 'guest'] ],
	['route' => 'anmeldung',                'roles' => ['guest', 'user'] ],
	['route' => 'anmeldung/form',           'roles' => ['guest', 'user'] ],
	['route' => 'anmeldung/confirm',           'roles' => ['guest', 'user'] ],
	['route' => 'anmeldung/ready',           'roles' => ['guest', 'user'] ],

	// tournament
	['route' => 'zfcadmin/tournament' ,          'roles' => ['administrator']],
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
	['route' => 'zfcadmin/runden/edit/hide',     'roles' => ['administrator']],
	['route' => 'zfcadmin/runden/edit/show',     'roles' => ['administrator']],
	['route' => 'zfcadmin/runden/edit/delete',   'roles' => ['administrator']],
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
	['route' => 'zfcadmin/teams/create',         'roles' => ['administrator']],
	['route' => 'zfcadmin/teams/edit',           'roles' => ['administrator']],
	// myteams
	['route' => 'zfcadmin/myteams',              'roles' => ['moderator']],
	['route' => 'zfcadmin/myteams/block',        'roles' => ['moderator']],
	['route' => 'zfcadmin/myteams/unblock',      'roles' => ['moderator']],
	['route' => 'zfcadmin/myteams/warn',         'roles' => ['moderator']],
	['route' => 'zfcadmin/myteams/warnPlayer',   'roles' => ['moderator']],
	['route' => 'zfcadmin/myteams/deleteWarning','roles' => ['moderator']],
	['route' => 'zfcadmin/myteams/anmerkung',    'roles' => ['moderator']],
	['route' => 'zfcadmin/myteams/player',       'roles' => ['moderator']],
	['route' => 'zfcadmin/myteams/addsub',       'roles' => ['moderator']],
	['route' => 'zfcadmin/myteams/makesub',      'roles' => ['moderator']],
	
	// site config
	['route' => 'zfcadmin/siteconfig/tournament',   'roles' => ['moderator']],
);

$ressources = array(
	'tournament',
);

$ressourceAllowRules = array(
	// config for navigation
	[['moderator'],     'tournament', 'round/viewHidden'],
	[['moderator'],     'tournament', 'debug/moderator'],
	[['administrator'], 'tournament', 'debug/administrator'],
);

return array(
    'controllers' => array(
		'aliases' => array(
			'index' => Controller\IndexController::class,
			'tournament' => Controller\TournamentController::class,
			'admin' => Controller\AdminController::class,
			'tournamentadmin' => Controller\TournamentAdminController::class,
			'roundcreator' => Controller\RoundCreatorController::class,
			'teamdmin' => Controller\TeamAdminController::class,
			'myteamadmin' => Controller\MyTeamAdminController::class,
			'anmeldung' => Controller\AnmeldungController::class,
		),
        'invokables' => array(
            Controller\IndexController::class => Controller\IndexController::class,
            Controller\TournamentController::class => Controller\TournamentController::class,
            Controller\AdminController::class => Controller\AdminController::class,
			Controller\TournamentAdminController::class => Controller\TournamentAdminController::class,
			Controller\RoundCreatorController::class => Controller\RoundCreatorController::class,
			Controller\TeamAdminController::class => Controller\TeamAdminController::class,
			Controller\MyTeamAdminController::class => Controller\MyTeamAdminController::class,
			Controller\AnmeldungController::class => Controller\AnmeldungController::class,
			Controller\PlayerController::class => Controller\PlayerController::class,
			Controller\TeilnehmerController::class => Controller\TeilnehmerController::class,
			Controller\TournamentConfigController::class => Controller\TournamentConfigController::class,
        ),
    ),
    
    
    'xelax' => $xelaxConfig,
    'router' => array(
        'routes' => include 'router.config.php',
    ),
    
	'bjyauthorize' => array(
		// resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => array(
            Provider\Resource\Config::class => $ressources,
        ),

		
		'rule_providers' => array(
			Provider\Rule\Config::class => array(
                'allow' => $ressourceAllowRules,

                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny' => array(
                    // ...
                ),
            ),
			\FSMPILoL\Provider\TournamentRuleProvider::class => array(),
		),
		
        'guards' => array(
            Guard\Route::class => $guardConfig
		),
	),
	
	/*'skelleton_application' => array(
		'registration_notification_from' => 'Gaming Group Aachen <schurix@gmx.de>',
		'registration_method_flag' => SkelletonOptions::REGISTRATION_METHOD_AUTO_ENABLE | SkelletonOptions::REGISTRATION_METHOD_SELF_CONFIRM,
		'registration_moderator_email' => array(
			'subject' => gettext_noop('[LeagueOfLegends] A new user has registered'),
			'template' => 'skelleton-application/email/register_moderator_notification'
		),
		'registration_user_email_welcome ' => array(
			'subject' => gettext_noop('[LeagueOfLegends] Welcome'),
			'template' => 'skelleton-application/email/register_welcome'
		),
		'registration_user_email_welcome_confirm_mail' => array(
			'subject' => gettext_noop('[LeagueOfLegends] Welcome. Please confirm your E-Mail'),
			'template' => 'skelleton-application/email/register_welcome_confirm_mail'
		),
		'registration_user_email_double_confirm' => array(
			'subject' => gettext_noop('[LeagueOfLegends] Welcome'),
			'template' => 'skelleton-application/email/register_double_confirm_mail'
		),
		'registration_user_email_confirm_mail' => array(
			'subject' => gettext_noop('[LeagueOfLegends] Welcome. Please confirm your E-Mail'),
			'template' => 'skelleton-application/email/register_confirm_mail'
		),
		'registration_user_email_confirm_moderator' => array(
			'subject' => gettext_noop('[LeagueOfLegends] Welcome'),
			'template' => 'skelleton-application/email/register_confirm_moderator'
		),
		'registration_user_email_activated' => array(
			'subject' => gettext_noop('[LeagueOfLegends] Your Account has been verified'),
			'template' => 'skelleton-application/email/register_activated'
		),
		'registration_user_email_disabled' => array(
			'subject' => gettext_noop('[LeagueOfLegends] Your Account has been disabled'),
			'template' => 'skelleton-application/email/register_disabled'
		),
	),*/
	
	
	
    'service_manager' => include 'service.config.php',
    
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    
	'view_manager' => array(
		'template_map' => array(
			'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
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
			array('label' => 'Home', 'route' => 'home', 'resource' => 'tournament', 'privilege' => 'navigation/home'),
			array('label' => 'Info', 'route' => 'info', 'resource' => 'tournament', 'privilege' => 'navigation/info'),
			array('label' => 'Anmeldung', 'route' => 'anmeldung/form', 'resource' => 'tournament', 'privilege' => 'navigation/anmeldung'),
			array('label' => 'Teilnehmer', 'route' => 'teilnehmer', 'resource' => 'tournament', 'privilege' => 'navigation/teilnehmer'),
			array('label' => 'Tabelle', 'route' => 'ergebnisse', 'resource' => 'tournament', 'privilege' => 'navigation/ergebnisse'),
			array('label' => 'Paarungen', 'route' => 'paarungen', 'resource' => 'tournament', 'privilege' => 'navigation/paarungen'),
			array('label' => 'Teilnehmer', 'route' => 'teams', 'resource' => 'tournament', 'privilege' => 'navigation/teams'),
			array('label' => 'Kontakt', 'route' => 'kontakt', 'resource' => 'tournament', 'privilege' => 'navigation/kontakt'),
		),
		'admin' => array(
			array('label' => gettext_noop('Tournaments'),           'route' => 'zfcadmin/tournament',        'resource' => 'tournament', 'privilege' => 'debug/administrator' ),
			array('label' => 'Paarungen', 'route' => 'zfcadmin/paarungen'),
			array('label' => 'Runden', 'route' => 'zfcadmin/runden'),
			array('label' => 'Teams', 'route' => 'zfcadmin/teams'),
			array('label' => 'Meine Teams', 'route' => 'zfcadmin/myteams'),
			'siteconfig' => array(
				'pages' => array(
					array('label' => gettext_noop('Tournament'),            'route' => 'zfcadmin/siteconfig/tournament', 'action' => 'index' , 'resource' => 'siteconfig', 'privilege' => 'tournament/list'),
				),
			),
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
					$tournament    = $services->get(Tournament\Tournament::class);
					$element->setTournament($tournament);
				}
			}
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
