<?php
/**
 * ZfcUserAdmin Configuration
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
$settings = array(
    

    /**
     * Mapper for ZfcUser
     *
     * Set the mapper to be used here
     * Currently Available mappers
     * 
     * ZfcUserAdmin\Mapper\UserDoctrine
     *
     * By default this is using
     * ZfcUserAdmin\Mapper\UserZendDb
     */
    'user_mapper' => 'ZfcUserAdmin\Mapper\UserDoctrine',
	'user_list_elements' => array('Id' => 'id', 'Name' => 'display_name', 'Email address' => 'email', 'Jabber' => 'jabber', 'Role' => 'role_name'),
	'create_user_auto_password' => false,
);

/**
 * You do not need to edit below this line
 */
return array(
    'zfcuseradmin' => $settings
);
