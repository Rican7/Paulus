<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful services
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2013 Trevor Suarez
 * @link        https://github.com/Rican7/Paulus
 * @version     2.0.0
 */

namespace Paulus;

use Klein\Route as KleinRoute;

/**
 * Route
 *
 * @uses	KleinRoute
 * @package Paulus
 */
class Route extends KleinRoute
{

    /**
     * Constants
     */

    /**
     * The prefix used to designate that the
     * callback in a route is to be automatically
     * handled by the automatically instantiated
     * controller for that route path
     *
     * @const string
     */
    const PAULUS_AUTO_ROUTE_PREFIX = '#';


    /**
     * Methods
     */

    /**
     * Check if a callback string is a prefixed with the
     * special Paulus auto-route-controller designation
     *
     * @param string $callback_string   The callback string to check
     * @static
     * @access public
     * @return boolean
     */
    public static function isPrefixedAsAutoRoute($callback_string)
    {
        return (strpos($callback_string, static::PAULUS_AUTO_ROUTE_PREFIX) === 0);
    }

    /**
     * Set the callback
     *
     * @param callable $callback    The route's callback to execute on matching
     * @throws InvalidArgumentException If the callback isn't a callable
     * @access public
     * @return Route
     */
    public function setCallback($callback)
    {
        // If its not prefixed as a Paulus auto-route, just have Klein handle it
        if (is_string($callback) && !static::isPrefixedAsAutoRoute($callback)) {
            parent::setCallback($callback);

        }

        $this->callback = $callback;

        return $this;
    }
}
