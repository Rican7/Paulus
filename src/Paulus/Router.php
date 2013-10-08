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
use LogicException;
use Paulus\Controller\AbstractController;
use Paulus\Exception\Http\EndpointNotFound;
use Paulus\Exception\Http\WrongMethod;
use Paulus\RouteFactory;
use Paulus\Support\Inflector;

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
     * Properties
     */

    /**
     * The namespace of the controllers
     *
     * @var string
     * @access protected
     */
    protected $controller_namespace;

    /**
     * The currently loaded controller
     *
     * @var Paulus\Controller\ControllerInterface
     * @access protected
     */
    protected $controller;


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
     * Set the controller namespace to be used
     * when automatically initializing controllers
     *
     * @param string $namespace
     * @access public
     * @return Router
     */
    public function setControllerNamespace($namespace)
    {
        // Don't allow this to be set more than once
        if (null !== $this->controller_namespace) {
            throw new LogicException('The controller namespace has already been set');
        }

        $this->controller_namespace = rtrim($namespace, '\\');

        return $this;
    }

    /**
     * Initialize and instantiate a new route controller
     * if a class exists for the given basename
     *
     * @param string $class_basename
     * @access public
     * @return boolean|Paulus\Controller\AbstractController
     */
    public function initializeController($class_basename)
    {
        $class_name = Inflector::urlNamespaceToClassNamespace($class_basename);

        $class = $this->controller_namespace . '\\'. $class_name;

        /**
         * Check if the class exists (Autoload it if its not loaded/included yet)
         * Find out if its a child of AbstractController, so we know its constructor
         */
        if (class_exists($class, true) && AbstractController::isChildClass($class)) {
            // Create our new controller, set it as such, and return it
            return $this->controller = new $class(
                $this->request,
                $this->response,
                $this->service,
                $this->app,
                $this
            );
        }

        return false;
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
