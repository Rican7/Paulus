<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.2
 */


// First things first... get our init time
define( 'PAULUS_START_TIME', microtime( true ) );


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
define( 'PAULUS_LIB_DIR', dir_rel_path( 'Paulus' ) );
define( 'PAULUS_APP_DIR',  dir_rel_path( 'application' ) );
define( 'PAULUS_EXTERNAL_LIB_DIR', dir_rel_path( 'vendor' ) );
define( 'PAULUS_MODELS_DIR', PAULUS_APP_DIR . 'Models' . DIRECTORY_SEPARATOR );
define( 'PAULUS_ROUTES_DIR', PAULUS_APP_DIR . 'routes' . DIRECTORY_SEPARATOR );

// Set some important include/require locations explicitly here
define( 'PAULUS_AUTOLOADER_LOCATION', PAULUS_LIB_DIR . 'AutoLoader.php' );
define( 'PAULUS_ROUTER_LOCATION', PAULUS_EXTERNAL_LIB_DIR . 'klein/klein.php' );

// Define our benchmark header (for both request and response here)
define( 'PAULUS_BENCHMARK_HEADER_NAME', 'X-Script-Benchmark' );

// Optional settings here (uncomment them to enable)
// define( 'PAULUS_INTERNAL_AUTOLOAD_DISABLED', null );
// define( 'PAULUS_APPLICATION_AUTOLOAD_DISABLED', null );
// define( 'PAULUS_EXTERNAL_AUTOLOAD_DISABLED', null );
// define( 'PAULUS_ALLOW_BENCHMARK_HEADER', null );
// define( 'PAULUS_BENCHMARK_ALWAYS', null );


// Let's bootstrap all the things
require_once( BASE_DIR . 'paulus_bootstrap.php' );
