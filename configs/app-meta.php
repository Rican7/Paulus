<?php

// Define the app's meta information
$load_config = function() {
	// Create a temp array so we can use some of the data in this same function
	$temp_array = array(
		'base_url' => '/',
		'app_protocol' => (!empty($_SERVER['HTTPS'])) ? 'https://' : 'http://',
	);

	return array_merge( $temp_array, array(
		'app_url' => $temp_array['app_protocol'] . $_SERVER['HTTP_HOST'] . $temp_array['base_url'],
		'title' => 'PHP API BoilerPlate',
	));
};
