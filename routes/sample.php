<?php

// Base response (Make the trailing splash optional to catch accidents and laziness)
respond( '/?', function( $request, $response, $app ) {
	// Show that we have access to the config
	//var_dump( $app->config );
	
	// Show some data to test our DB connection
	var_dump( Api::get_sample_data() );
});
