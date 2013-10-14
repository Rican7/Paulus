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

use Klein\AbstractResponse;
use Klein\DataCollection\RouteCollection;
use Klein\Klein;
use Klein\Request;
use Klein\Route;
use Klein\ServiceProvider;
use LogicException;
use Paulus\Controller\AbstractController;
use Paulus\Controller\ControllerInterface;
use Paulus\Exception\Http\EndpointNotFound;
use Paulus\Exception\Http\WrongMethod;
use Paulus\Request\AutomaticParamsParserRequest;
use Paulus\Response\ApiResponse;
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

        $this->routes        = $routes        ?: new RouteCollection();
        $this->route_factory = $route_factory ?: new RouteFactory();

        // Ignore their app entry, always keep as null
        $this->app           = null;
    }

    /**
     * Returns the app object
     *
     * @access public
     * @return Paulus
     */
    public function app()
    {
        // Alert them of their logical issue
        if (null === $this->app) {
            throw new LogicException('A Paulus instance has not yet been bound to the router');
        }

        return parent::app();
    }

    /**
     * Bind a Paulus application instance
     * to the current router instance
     *
     * @param Paulus $app
     * @access public
     * @return Router
     */
    public function bindPaulusApp(Paulus $app)
    {
        // Don't allow this to be set more than once
        if (null !== $this->app) {
            throw new LogicException('A Paulus instance has already been set');
        }

        $this->app = $app;

        return $this;
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
                $this->app(),
                $this
            );
        } else {
            // Write to our log
            $this->app()->logger()->debug(
                sprintf(
                    'Controller couldn\'t be initialized. Class %s doesn\'t exist or doesn\'t extend %s',
                    $class,
                    'Paulus\Controller\AbstractController'
                )
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
            function ($request, $response, $service, $app, $router, $matched, $methods_matched) {
                // Don't error out on an OPTIONS request
                if (!$request->method('OPTIONS')) {
                    $exception = new WrongMethod();

                    // Tell them of the possible methods
                    $exception->setMoreInfo(
                        [
                            'possible_methods' => $methods_matched,
                        ]
                    );

                    throw $exception;

                }
            }
        )->setName(405);

        return $this;
    }

    /**
     * Get the current controller's exception handler
     * if there is one defined
     *
     * @access public
     * @return callable
     */
    public function getControllerExceptionHandler()
    {
        // If the controller implements our interface
        if ($this->controller instanceof ControllerInterface) {

            return [$this->controller, 'handleException'];
        }

        return false;
    }

    /**
     * Get the current controller's result handler
     * if there is one defined
     *
     * @access public
     * @return callable
     */
    public function getControllerResultHandler()
    {
        // If the controller implements our interface
        if ($this->controller instanceof ControllerInterface) {

            return [$this->controller, 'handleResult'];
        }

        return false;
    }

    /**
     * Wrap's the given route's callback method
     * in a given callable handler for
     * post-processing and filtering
     *
     * @param Route $route
     * @param callable $handler
     * @access protected
     * @return Route
     */
    protected function wrapRouteCallbackInResultHandler(Route $route, callable $handler)
    {
        // Only modify the route's callback if its not protected
        if ($route->getIsProtected() !== true) {
            $callback = $route->getCallback();

            // If the callback is a controller auto-route, then redefine its callback
            if (is_string($callback) && $route->isPrefixedAsAutoRoute($callback)) {
                $callback = [$this->controller, $route->stripAutoRoutePrefix($callback)];
            }

            // Wrap the callback in the handler
            $route->setCallback(
                function () use ($handler, $callback) {
                    return $handler(
                        call_user_func_array(
                            $callback,
                            func_get_args()
                        )
                    );
                }
            );
        }

        return $route;
    }

    /**
     * Handle the route's callback
     *
     * @see Klein\Klein::handleRouteCallback()
     * @param Route $route
     * @param RouteCollection $matched
     * @param mixed $methods_matched
     * @access protected
     * @return void
     */
    protected function handleRouteCallback(Route $route, RouteCollection $matched, $methods_matched)
    {
        // Get the result handler of the current controller
        $handler = $this->getControllerResultHandler();

        // Let's wrap our route's current callback in the handler
        if ($handler !== false) {
            $this->wrapRouteCallbackInResultHandler($route, $handler);
        }

        parent::handleRouteCallback($route, $matched, $methods_matched);
    }

    /**
     * Dispatch the request to the approriate route(s)
     *
     * @see Klein\Klein::dispatch()
     * @param Request $request              The request object to give to each callback
     * @param AbstractResponse $response    The response object to give to each callback
     * @param boolean $send_response        Whether or not to "send" the response after the last route has been matched
     * @param int $capture                  Specify a DISPATCH_* constant to change the output capturing behavior
     * @access public
     * @return void|string
     */
    public function dispatch(
        Request $request = null,
        AbstractResponse $response = null,
        $send_response = true,
        $capture = self::DISPATCH_NO_CAPTURE
    ) {
        // Change our defaults
        $request  = $request  ?: AutomaticParamsParserRequest::createFromGlobals();
        $response = $response ?: $this->app()->getDefaultResponse();

        return parent::dispatch($request, $response, $send_response, $capture);
    }
}
