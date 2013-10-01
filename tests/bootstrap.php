<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful services
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2013 Trevor Suarez
 * @link        https://github.com/Rican7/Paulus
 * @license     https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version     2.0.0
 */

// Turn on all errors for our unit testing
error_reporting(-1);

// Load our autoloader, and add our Test class namespace
$autoloader = require(__DIR__ . '/../vendor/autoload.php');
$autoloader->add('Paulus\Tests', __DIR__);
