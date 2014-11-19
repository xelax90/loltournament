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
			'main' => array(
				'token' => 'ababababa',
				'modules' => array(
					'jquery',
					'iscroll',
				)
			),
			'admin' => array(
				'token' => 'aababa',
				'modules' => array(
					'jquery',
					'iscroll',
					'bootstrap',
					"Selecter",
					"Stepper",
					"iCheck",
					"bootstrap-switch",
					'lightbox',
				)
			),
			'ieLT9' => array(
				'token' => 'bababab',
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
