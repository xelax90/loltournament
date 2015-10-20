<?php
return array(
    'bower' => array(
        'bower_folder' => array(
            'os' => 'bower_components',
        ),
        'pack_folder' => array(
            'os' => 'public/js',
            'web' => '/js',
        ),
        'debug_folder' => array(
            'os' => 'public/js/dev',
            'web' => '/js/dev',
        ),
        'debug_mode' => true,
		'packs' => array(
			'frontend' => array(
				'token' => 'main',
				'modules' => array(
					'jquery',
					'iscroll',
				)
			),
			'backend' => array(
				'token' => 'admin',
				'modules' => array(
					'jquery',
					'iscroll',
					'bootstrap',
					'bootstrap-switch',
					"Selecter",
					"Stepper",
					"iCheck",
					'lightbox',
					'select2',
				)
			),
			'ieLT9' => array(
				'token' => 'ieLT9',
				'modules' => array(
					'html5shiv',
					'respond',
				),
				'attributes' =>  array(
					'conditional' => 'lt IE 9',
				),
			)
		)
    ),
);
