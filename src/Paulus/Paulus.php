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

use BadMethodCallException;

/**
 * Paulus
 *
 * Main Paulus application class
 *
 * @package Paulus
 */
class Paulus
{

    /**
     * Properties
     */

    /**
     * The time that Paulus first booted up
     *
     * @var int
     * @access protected
     */
    protected $start_time;

    /**
     * The HTTP router
     *
     * @var Router
     * @access protected
     */
    protected $router;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param array $config     A custom application configuration array
     * @param Router $router    The Router instance to use for HTTP routing
     * @access public
     */
    public function __construct(array $config = null, Router $router = null)
    {
        // First things first... get our init time
        $this->start_time = microtime(true);

        // TODO: Handle config merging

        // Set our router with a context of this application instance
        $this->router = $router ?: new Router(null, $this);
    }

    /**
     * Get the time that Paulus started
     *
     * @access public
     * @return int
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Get the HTTP router instance
     *
     * @access public
     * @return Router
     */
    public function router()
    {
        return $this->router;
    }

    /**
     * Magic "call" method
     *
     * @param string $method
     * @param array $args
     * @access public
     * @return mixed
     */
    public function __call($method, array $args)
    {
        // Make sure the method actually exists...
        if (!method_exists($this->router(), $method)) {
            throw new BadMethodCallException(
                'Unknown method '. get_class($this->router()) .'::'. $method
            );
        }

        call_user_func_array([$this->router(), $method], $arguments);

        return $this;
    }
}