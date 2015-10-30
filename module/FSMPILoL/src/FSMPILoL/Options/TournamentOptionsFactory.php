<?php
namespace FSMPILoL\Options;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use XelaxSiteConfig\Options\Service\SiteConfigService;

/**
 * Description of SiteRegistrationOptionsFactory
 *
 * @author schurix
 */
class TournamentOptionsFactory implements FactoryInterface {
	const CONFIG_PREFIX = 'fsmpi_lol.tournament';
	
    public function createService(ServiceLocatorInterface $serviceLocator) {
		/* @var $siteConfigService SiteConfigService */
		$siteConfigService = $serviceLocator->get(SiteConfigService::class);
		$config = $siteConfigService->getConfig(static::CONFIG_PREFIX);
        return new TournamentOptions($config);
    }
}
