<?php
namespace FSMPILoL;

use XelaxAdmin\Router\ListRoute;

$routerConfig = array(
	'home' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/',
			'defaults' => array(
				'controller' => Controller\IndexController::class,
				'action'     => 'index',
			),
		),
	),
	'info' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/info',
			'defaults' => array(
				'controller' => Controller\IndexController::class,
				'action'     => 'info',
			),
		),
	),
	'kontakt' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/kontakt',
			'defaults' => array(
				'controller' => Controller\IndexController::class,
				'action'     => 'kontakt',
			),
		),
	),
	'ergebnisse' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/ergebnisse',
			'defaults' => array(
				'controller' => Controller\TournamentController::class,
				'action'     => 'ergebnisse',
			),
		),
	),
	'paarungen' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/paarungen',
			'defaults' => array(
				'controller' => Controller\TournamentController::class,
				'action'     => 'paarungen',
			),
		),
	),
	'meldung' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/meldung',
			'defaults' => array(
				'controller' => Controller\TournamentController::class,
				'action'     => 'meldung',
			),
		),
	),
	'teams' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/teams',
			'defaults' => array(
				'controller' => Controller\TournamentController::class,
				'action'     => 'teams',
			),
		),
	),
	'teilnehmer' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/teilnehmer',
			'defaults' => array(
				'controller' => Controller\TeilnehmerController::class,
				'action'     => 'teilnehmer',
			),
		),
	),
	'myteam' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/myteam',
			'defaults' => array(
				'controller' => Controller\TournamentController::class,
				'action'     => 'myteam',
			),
		),
	),
	'anmeldung' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/anmeldung',
			'defaults' => array(
				'controller' => Controller\AnmeldungController::class,
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
						'controller' => Controller\AnmeldungController::class,
						'action'     => 'form',
					),
				),
			),
			'confirm' => array(
				'type' => 'literal',
				'options' => array(
					'route'    => '/confirm',
					'defaults' => array(
						'controller' => Controller\AnmeldungController::class,
						'action'     => 'confirm',
					),
				),
			),
			'ready' => array(
				'type' => 'literal',
				'options' => array(
					'route'    => '/ready',
					'defaults' => array(
						'controller' => Controller\AnmeldungController::class,
						'action'     => 'ready',
					),
				),
			)
		)
	),
	'zfcadmin' => array(
		'options' => array(
			'defaults' => array(
				'controller' => Controller\AdminController::class,
				'action' => 'index',
			)
		),
		'child_routes' => array(
			'tournament'        => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'tournament'        ) ),
			'paarungen' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/paarungen[/:match_id]',
					'defaults' => array(
						'controller' => Controller\TournamentAdminController::class,
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
								'controller' => Controller\TournamentAdminController::class,
								'action'     => 'paarungBlock',
							),
						),
					),
					'unblock' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/unblock',
							'defaults' => array(
								'controller' => Controller\TournamentAdminController::class,
								'action'     => 'paarungUnblock',
							),
						),
					),
					'comment' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/comment',
							'defaults' => array(
								'controller' => Controller\TournamentAdminController::class,
								'action'     => 'paarungComment',
							),
						),
					),
					'setresult' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/setresult',
							'defaults' => array(
								'controller' => Controller\TournamentAdminController::class,
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
						'controller' => Controller\RoundCreatorController::class,
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
								'controller' => Controller\RoundCreatorController::class,
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
								'controller' => Controller\RoundCreatorController::class,
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
										'controller' => Controller\RoundCreatorController::class,
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
										'controller' => Controller\RoundCreatorController::class,
										'action'     => 'show',
									),
								),
							),
							'delete' => array(
								'type' => 'literal',
								'options' => array(
									'route'    => '/delete',
									'defaults' => array(
										'controller' => Controller\RoundCreatorController::class,
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
						'controller' => Controller\TeamAdminController::class,
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
								'controller' => Controller\TeamAdminController::class,
								'action' => 'anmerkung',
							)
						),
					),
					'block' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/block',
							'defaults' => array(
								'controller' => Controller\TeamAdminController::class,
								'action' => 'block',
							)
						),
					),
					'unblock' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/unblock',
							'defaults' => array(
								'controller' => Controller\TeamAdminController::class,
								'action' => 'unblock',
							)
						),
					),
					'warn' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/warn',
							'defaults' => array(
								'controller' => Controller\TeamAdminController::class,
								'action' => 'warn',
							)
						),
					),
					'warnPlayer' => array(
						'type' => 'segment',
						'options' => array(
							'route'    => '/warnPlayer/:player_id',
							'defaults' => array(
								'controller' => Controller\TeamAdminController::class,
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
								'controller' => Controller\TeamAdminController::class,
								'action' => 'deleteWarning',
								'warning_id' => 0,
							),
							'constraints' => array(
								'warning_id'         => '[0-9]*',
							),
						),
					),
					'player' => array(
						'type' => ListRoute::class,
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
								'controller' => Controller\TeamAdminController::class,
								'action' => 'addsub',
							)
						),
					),
					'makesub' => array(
						'type' => 'segment',
						'options' => array(
							'route'    => '/makesub/:player_id',
							'defaults' => array(
								'controller' => Controller\TeamAdminController::class,
								'action' => 'makesub',
								'player_id' => 0,
							),
							'constraints' => array(
								'player_id'         => '[0-9]*',
							),
						),
					),
					'create' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/create',
							'defaults' => array(
								'controller' => Controller\TeamAdminController::class,
								'action' => 'create',
							)
						),
					),
					'edit' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/edit',
							'defaults' => array(
								'controller' => Controller\TeamAdminController::class,
								'action' => 'edit',
							)
						),
					),
				),
			),
			'myteams' => array(
				'type' => 'segment',
				'options' => array(
					'route'    => '/myteams[/:team_id]',
					'defaults' => array(
						'controller' => Controller\MyTeamAdminController::class,
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
								'controller' => Controller\MyTeamAdminController::class,
								'action' => 'anmerkung',
							)
						),
					),
					'block' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/block',
							'defaults' => array(
								'controller' => Controller\MyTeamAdminController::class,
								'action' => 'block',
							)
						),
					),
					'unblock' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/unblock',
							'defaults' => array(
								'controller' => Controller\MyTeamAdminController::class,
								'action' => 'unblock',
							)
						),
					),
					'warn' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/warn',
							'defaults' => array(
								'controller' => Controller\MyTeamAdminController::class,
								'action' => 'warn',
							)
						),
					),
					'warnPlayer' => array(
						'type' => 'segment',
						'options' => array(
							'route'    => '/warnPlayer/:player_id',
							'defaults' => array(
								'controller' => Controller\MyTeamAdminController::class,
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
								'controller' => Controller\MyTeamAdminController::class,
								'action' => 'deleteWarning',
								'warning_id' => 0,
							),
							'constraints' => array(
								'warning_id'         => '[0-9]*',
							),
						),
					),
					'player' => array(
						'type' => ListRoute::class,
						'options' => array(
							// the config key of the options
							'controller_options_name' => 'myPlayer',
						),
					),
					'addsub' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/addsub',
							'defaults' => array(
								'controller' => Controller\MyTeamAdminController::class,
								'action' => 'addsub',
							)
						),
					),
					'makesub' => array(
						'type' => 'segment',
						'options' => array(
							'route'    => '/makesub/:player_id',
							'defaults' => array(
								'controller' => Controller\MyTeamAdminController::class,
								'action' => 'makesub',
								'player_id' => 0,
							),
							'constraints' => array(
								'player_id'         => '[0-9]*',
							),
						),
					),
					'edit' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/edit',
							'defaults' => array(
								'controller' => Controller\MyTeamAdminController::class,
								'action' => 'edit',
							)
						),
					),
				),
			),
		),
	),
);

return $routerConfig;