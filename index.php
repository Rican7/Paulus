<?php

/*****************************************/
//          PHP Api Boilerplate          //
//                                       //
//        Created by Trevor Suarez       //
//     Inspired by and Helped by Many    //
//          Refer to the README          //
//                                       //
/*****************************************/

/*
 * Set some low-level configurations that we
 * may want to change before loading our
 * application's "config"
 */

// Set our base directory here
define( 'BASE_DIR', __DIR__ . DIRECTORY_SEPARATOR );


// Quick function to make directory defining easier
function dir_rel_path( $dir_name ) {
	return BASE_DIR . $dir_name . DIRECTORY_SEPARATOR;
}


// Set some Paulus directories here
define( 'PAULUS_CONFIG_DIR', dir_rel_path( 'configs' ) );
define( 'PAULUS_LIB_DIR', dir_rel_path( 'libs' ) );
define( 'PAULUS_EXTERNAL_LIB_DIR', dir_rel_path( 'external-libs' ) );
define( 'PAULUS_ROUTES_DIR', dir_rel_path( 'routes' ) );


// Let's bootstrap all the things
require_once( BASE_DIR . 'paulus_bootstrap.php' );
