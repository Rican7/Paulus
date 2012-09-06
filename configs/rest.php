<?php

// Define the REST configuration
$load_config = function() {
	return array(
		'defaults' => array(
			'status' => 'NULL',
			'data-type' => 'json',
			'jsonp-padding' => 'callback',
		),
		'status-codes' => array(
			200 => 'OK',
			404 => 'NOT_FOUND',
		),
		'mime-types' => array(
			'json' => 'application/json',
			'jsonp' => 'application/javascript',
			//'xml' => 'application/xml',
			//'csv' => 'application/csv',
		),
		'http-access-control' => array(
			'allow-headers' => 'Origin, X-Requested-With, Content-Type, Accept',
			'allow-methods' => 'POST, GET',
			'allow-origin' => '*',
		),
	);
};
