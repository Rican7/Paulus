<?php

// Base response (Make the trailing splash optional to catch accidents and laziness)
respond( '/[a:key]?', function( $request, $response, $app ) {
	// Get our sent parameter
	$key_to_encrypt = $request->key;

	// Show some sample data
	$response->data = Api::get_sample_data( $key_to_encrypt );
});

// Get a sample of user data
respond( 'POST', '/user/profile/?', function( $request, $response, $app ) {
	// Get our sent parameter
	$user_id = $request->param( 'id' );

	// Did we get our required params?
	if ( is_null( $user_id ) ) {
		$response->abort( 400, null, 'A requested user id was not sent' );
	}

	// Let's get the "user's profile"
	$sample_profile = Api::get_sample_user_data( $user_id );

	if ( !is_null($sample_profile) ) {
		// Show some data for testing
		$response->data = $sample_profile;
	}
	else {
		$response->abort( 404, null, 'A user with that id was not found.' );
	}
});

// Get a sample of database data
respond( '/user/db/?', function( $request, $response, $app ) {
	// Show some data to test our DB connection
	$response->data = Api::get_sample_db_data();
});
