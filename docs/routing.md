# Routing

### Klein Based

The routing engine (by default) built-in to Paulus, is provided by [Klein.php](//github.com/chriso/klein.php).
For more information dealing SOLELY with the routing engine, you can take a look at Klein's [README](//github.com/chriso/klein.php/blob/master/README.md).

## Basics

Basically, a route is a definition with some options. You simply tell the Routing engine what kind of requests you'd like it to respond to, and then how you'd like to respond.

_Example_

```php
<?php

// Base response (Make the trailing splash optional to catch accidents and laziness)
Router::route( '/?', function( $request, $response, $service ) {
	// Get our sent parameter
	$search_query = $request->param( 'query' );

	// Set our response data
	$response->data = 'You queried/searched for: ' . $search_query;
});
```

For added legibility and overall convenience, their are many ways to achieve a similar result through the routing engine. For example, all of the following do the same thing/respond to this type of request: "*GET* /"

```php
<?php

Router::route( function( $request, $response, $service ) {
	// Exit with a message, raw-style
	exit( 'yup, this works' );
});

Router::route( '/?', function( $request, $response, $service ) {
	// Exit with a message, raw-style
	exit( 'this also works' );
});

Router::route( 'GET', '/?', function( $request, $response, $service ) {
	// Exit with a message, raw-style
	exit( 'this also works' );
});

Router::get( function( $request, $response, $service ) {
	// Exit with a message, raw-style
	exit( 'this also works' );
});

Router::get( '/?', function( $request, $response, $service ) {
	// Exit with a message, raw-style
	exit( 'this also works' );
});

Router::any( function( $request, $response, $service ) {
	// Exit with a message, raw-style
	exit( 'this also works' );
});

Router::any( '/?', function( $request, $response, $service ) {
	// Exit with a message, raw-style
	exit( 'this also works' );
});

```
