<?php
namespace FSMPILoL;

use Zend\Cache\StorageFactory;
use FSMPILoL\Cache\Storage\Adapter\Filesystem;
use Doctrine\ORM\EntityManager;

return array(
	'factories' => array(
		'FSMPILoL\Log' => function ($sm) {
			$log = new Zend\Log\Logger();
			$writer = new Zend\Log\Writer\Stream('./data/logs/fsmpilol.log');
			$log->addWriter($writer);
			return $log;
		},
		'FSMPILoL\RiotCache' => function (){
			$cache = StorageFactory::factory(array(
				'adapter' => array(
					'name' => Filesystem::class,
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
			$cache = StorageFactory::factory(array(
				'adapter' => array(
					'name' => Filesystem::class,
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

			$cache = StorageFactory::factory(array(
				'adapter' => array(
					'name' => Filesystem::class,
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
		Options\APIOptions::class => function ($sm) {
			$config = $sm->get('Config');
			return new Options\APIOptions(isset($config['fsmpilol_api']) ? $config['fsmpilol_api'] : array());
		},
		Options\AnmeldungOptions::class => function ($sm) {
			$config = $sm->get('Config');
			return new Options\AnmeldungOptions(isset($config['fsmpilol_anmeldung']) ? $config['fsmpilol_anmeldung'] : array());
		},
		Options\RoundCreatorOptions::class => function ($sm) {
			$config = $sm->get('Config');
			return new Options\RoundCreatorOptions(isset($config['fsmpilol_roundcreator']) ? $config['fsmpilol_roundcreator'] : array());
		},
		Service\Tournament\Permission::class => function($sm){
			return new Service\Tournament\Permission();
		},
		'StreamNavigation' => Navigation\Service\StreamNavigationFactory::class,
		Entity\Tournament::class => function($sm){
			/* @var $anmeldungOptions Options\AnmeldungOptions */
			$anmeldungOptions = $sm->get(Options\AnmeldungOptions::class);
			/* @var $em EntityManager */
			$em = $sm->get(EntityManager::class);
			
			$tournamentId = $anmeldungOptions->getTournamentId();
			return $em->getRepository(Entity\Tournament::class)->find($tournamentId);
		},
	),
	'invokables' => array(
		Tournament\Anmeldung::class => Tournament\Anmeldung::class,
		Riot\RiotAPI::class => Riot\RiotAPI::class,
		Tournament\Tournament::class => Tournament\Tournament::class,
	),
	'aliases' => array(
		'FSMPILoL\Options\API' => Options\APIOptions::class,
		'FSMPILoL\Options\Anmeldung' => Options\AnmeldungOptions::class,
		'FSMPILoL\Options\RoundCreator' => Options\RoundCreatorOptions::class,
		'FSMPILoL\Tournament\Permission' => Service\Tournament\Permission::class
	),
	'initializers' => array(
		'TournamentInitializer' => function($instance, $sm){
			if($instance instanceof Tournament\TournamentAwareInterface){
				$tournament = $sm->get(Entity\Tournament::class);
				$instance->setTournament($tournament);
			}
		}
	),
);