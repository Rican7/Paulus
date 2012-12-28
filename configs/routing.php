<?php

// Define all of our routes (we could do this automatically, but we'd like more control)
$load_config = function() {
	return array(
		'load_all_automatically' => false,
		'routes' => array(
			'index',
			'sample',
		),
		'top_level_route' => 'index',
		'auto_start_controllers' => true,
		'pass_app_to_service' => true,
		'controller_base_namespace' => 'Api',
	);
};
