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
