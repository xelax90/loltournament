<?php

$bjyConfig = array(
	// Using the authentication identity provider, which basically reads the roles from the auth service's identity
	'identity_provider' => BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider::class,

	'role_providers'        => array(
		// using an object repository (entity repository) to load all roles into our ACL
		BjyAuthorize\Provider\Role\ObjectRepositoryProvider::class => array(
			'object_manager'    => Doctrine\ORM\EntityManager::class,
			'role_entity_class' => SkelletonApplication\Entity\Role::class,
		),
	),
	
	'unauthorized_strategy' => BjyAuthorize\View\RedirectionStrategy::class,
);

return array(
	'bjyauthorize' => $bjyConfig
);