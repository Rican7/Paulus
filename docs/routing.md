# Routing

### Klein Based

The routing engine (by default) built-in to Paulus, is provided by [Klein.php](//github.com/chriso/klein.php).
For more information dealing SOLELY with the routing engine, you can take a look at Klein's [README](//github.com/chriso/klein.php/blob/master/README.md).

## Contents
- [Basics](#basics)
- [Namespacing](#namespacing)

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

See more **Klein** capabilities at its [official documentation](//github.com/chriso/klein.php/blob/master/README.md)

## Namespacing
_Or different routing files and why you'll want to use them._

A full-featured REST API can become quite large pretty easily. Also, a properly RESTful API should seperate its different accessible objects as their own pseudo-subdirectory.
In order to allow the easy seperation of different endpoints or parts of your API, the routing engine can handle splitting up your different routing files into "namespaces".

### Namespaced Route Files

Although you could define all of your routes in one "top level" route file, that could get quite messy and large. The solution, is to separate your routes into different files. How this works, is you simply name the file in your routes folder with a filename that corresponds with the endpoint.

_Example_ - http://api.example.com/users - users.php

```php
<?php

// Will match/respond to /users because its defined in the "users.php" route file
Router::route( '/?', function( $request, $response, $service ) {
	// Do something
});

// Will match/respond to /users/12345 (and other integers) because its defined in the "users.php" route file
Router::route( '/[i:id]', function( $request, $response, $service ) {
	// Do something else
});
```

### Top Level Routing

Most of the time, a REST API will have its different accesible endpoints under different namespaces. However, there may be some cases where you'll want to have route responders for the top-level ( "/" ). In that case, there is a special [configuration option](configuration.md#routing) for defining a specific route file to respond to the top-level requests. If unchanged, the default behavior is to default to "index" (routes/index.php).

If you'd prefer, or you just have a smaller/lighter app, you can actually define all of your routes in this one file. Just keep in mind that if you also have namespaced route files that those responses could clash with the top-level responses.

_Example_ - index.php

```php
<?php

Router::route( '/?', function( $request, $response, $service ) {
	// Do something
});

Router::route( '/users/?', function( $request, $response, $service ) {
	// Could possibly clash with a "users.php" route file
});
```
