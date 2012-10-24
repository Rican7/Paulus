<?php

// Define the REST configuration
$load_config = function() {
	return array(
		'defaults' => array(
			'status' => 'NULL',
			'data-type' => 'json',
			'jsonp-padding' => 'callback',
			'allowed-methods' => array(
				'GET',
				'POST',
			),
		),
		'status-codes' => array(
			200 => 'OK',
			400 => 'BAD_REQUEST',
			401 => 'UNAUTHORIZED',
			403 => 'FORBIDDEN',
			404 => 'NOT_FOUND',
			405 => 'METHOD_NOT_ALLOWED',
			406 => 'NOT_ACCEPTABLE',
			429 => 'RATE_LIMITED',
			500 => 'INTERNAL_SERVER_ERROR',
			502 => 'BAD_GATEWAY',
			503 => 'SERVICE_UNAVAILABLE',
			504 => 'GATEWAY_TIMEOUT',
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
