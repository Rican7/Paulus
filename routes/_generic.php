<?php

// Generic route to define global route objects
// Designed to help with instanciating objects and making them available in all other routes (lazy-loading)
respond( function( $request, $response, $app ) {
	// Put an object here and make it available anywhere, so you don't have to modify the index (core/bootstrap) file
	// EX: $app->auth = new AuthLibrary(); // Now its available in any other route
});
