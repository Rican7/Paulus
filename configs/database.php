<?php

// Define the database configuration
$load_config = function() {
	return array(
		'connections' => array(
			'development' => 'mysql://username:password@localhost/development',
			'production' => 'mysql://username:password@localhost/production',
			'test' => 'mysql://username:password@localhost/test',
		),
		'default_connection' => 'development',
		'model_directory' => 'models',
	);
};
