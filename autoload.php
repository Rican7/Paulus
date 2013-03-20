<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.4
 */


// Set Paulus' base directory here
define( 'PAULUS_BASE_DIR', __DIR__ . DIRECTORY_SEPARATOR );

// Require our AutoLoader
require_once( PAULUS_BASE_DIR . 'Paulus' . DIRECTORY_SEPARATOR . 'AutoLoader.php' );

// Instanciate our AutoLoader
$autoloader = new \Paulus\AutoLoader();
$autoloader->register_internal_autoloader();
