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

use Klein\DataCollection\RouteCollection;
use Klein\Klein;
use Klein\ServiceProvider;
use Paulus\Exception\Http\EndpointNotFound;
use Paulus\Exception\Http\WrongMethod;
use Paulus\RouteFactory;

/**
 * Router
 *
 * Router extension of Klein
 *
 * @uses    Klein
 * @package Paulus
 */
class Router extends Klein
{

    /**
     * Methods
     */

    /**
     * Constructor
     *
     * Create a new Router instance with optionally injected dependencies
     *
     * @param ServiceProvider $service              Service provider object responsible for utilitarian behaviors
     * @param mixed $app                            An object passed to each route callback, defaults to an App instance
     * @param RouteCollection $routes               Collection object responsible for containing all route instances
     * @param AbstractRouteFactory $route_factory   A factory class responsible for creating Route instances
     * @access public
     */
    public function __construct(
        ServiceProvider $service = null,
        $app = null,
        RouteCollection $routes = null,
        AbstractRouteFactory $route_factory = null
    ) {
        // Instanciate and fall back to defaults
        $this->service       = $service       ?: new ServiceProvider();
        $this->app           = $app           ?: new Paulus(); // Replace with current application instance
        $this->routes        = $routes        ?: new RouteCollection();
        $this->route_factory = $route_factory ?: new RouteFactory();
    }

    /**
     * Setup our default error route responders
     * by registering them with our router
     *
     * @access public
     * @return Router
     */
    public function setupDefaultErrorRoutes()
    {
        // "HTTP 404 Not Found" default handler
        $this->respond(
            404,
            function () {
                throw new EndpointNotFound();
            }
        )->setName(404);

        // "HTTP 405 Method Not Allowed" default handler
        $this->respond(
            405,
            function () {
                throw new WrongMethod();
            }
        )->setName(405);

        return $this;
    }
}
