<?php

use	\Paulus\Router;

// Base response (Make the trailing splash optional to catch accidents and laziness)
Router::get( '/', function( $request, $response, $service ) {
	// Show some sample data
	$response->data = 'You\'ve hit the ' . $service->app->parse( '{APP_TITLE}' ) . ' API';
});
