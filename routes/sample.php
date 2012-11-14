<?php

use	Api\Sample,
	\Paulus\Router;

// Base response (Make the trailing splash optional to catch accidents and laziness)
Router::route( '/[a:key]', function( $request, $response, $service ) {
	// Get our sent parameter
	$key_to_encrypt = $request->key;

	// Show some sample data
	$response->data = Sample::get_sample_data( $key_to_encrypt );
});

// Get a sample of user data
Router::route( 'POST', '/user/profile', function( $request, $response, $service ) {
	// Get our sent parameter
	$user_id = $request->param( 'id' );

	// Did we get our required params?
	if ( is_null( $user_id ) ) {
		$service->app->abort( 400, null, 'A requested user id was not sent' );
	}

	// Let's get the "user's profile"
	$sample_profile = Sample::get_sample_user_data( $user_id );

	if ( !is_null($sample_profile) ) {
		// Show some data for testing
		$response->data = $sample_profile;
	}
	else {
		$service->app->abort( 404, null, 'A user with that id was not found.' );
	}
});

// Get a sample of database data
Router::route( '/user/db', function( $request, $response, $service ) {
	// Show some data to test our DB connection
	$response->data = Sample::get_sample_db_data();
});

// Showing off the aliasing
Router::hub( 'GET', '/test/quick', '\Api\Sample::get_sample_data');

// Controller special
Router::hub( 'GET', '/test/donkey', function( $request, $response, $service ) {
	// Let's return false so we can test/show how our controller's responder works
	return false;
});

// Controller special
Router::hub( '/test/donk', function( $request, $response, $service ) {
	// Let's return false so we can test/show how our controller's responder works
	return 'donk';
});
