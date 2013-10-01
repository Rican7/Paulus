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

namespace Paulus;

use Klein\RouteFactory as KleinRouteFactory;

/**
 * RouteFactory
 *
 * @uses    KleinRouteFactory
 * @package Paulus
 */
class RouteFactory extends KleinRouteFactory
{

    /**
     * Build a Route instance
     *
     * @param callable $callback    Callable callback method to execute on route match
     * @param string $path          Route URI path to match
     * @param string|array $method  HTTP Method to match
     * @param boolean $count_match  Whether or not to count the route as a match when counting total matches
     * @param string $name          The name of the route
     * @static
     * @access public
     * @return Route
     */
    public function build($callback, $path = null, $method = null, $count_match = true, $name = null)
    {
        return new Route(
            $callback,
            $this->preprocessPathString($path),
            $method,
            $this->shouldPathStringCauseRouteMatch($path) // Ignore the $count_match boolean that they passed
        );
    }
}
