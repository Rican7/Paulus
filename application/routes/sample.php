<?php

use	\Controllers\Api\Sample,
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

// Controller special
Router::channel( 'GET', '/test/quick', array( '\Api\Sample', 'get_sample_data' ) );

// Controller special
Router::channel( 'GET', '/test/donkey', function( $request, $response, $service ) {
	// Let's return false so we can test/show how our controller's responder works
	return false;
});

// Controller special
Router::channel( 'GET', '/test/nothing', function( $request, $response, $service ) {
	// Let's return null so we can test/show how our controller's responder works
	return null;
});

// Controller special
Router::channel( '/test/donk', function( $request, $response, $service ) {
	// Let's return some random data so we can test/show how our controller's responder works
	return 'donk';
});

// Controller special
Router::channel( '/test/copyright', function( $request, $response, $service ) {
	// Let's return some template data so we can test/show how our template parser works
	return array(
		'Copyright' => '{COPYRIGHT}',
		'creator_website' => '{TREVOR_URL}',
	);
});

// Controller special
Router::channel( '/test/copyright/manual', function( $request, $response, $service ) {
	// Let's return some template data so we can test/show how our template parser works
	return array(
		'Copyright' => $service->app->parse( '{COPYRIGHT}' ),
		'creator_website' => $service->app->parse( '{TREVOR_URL}' ),
	);
});
