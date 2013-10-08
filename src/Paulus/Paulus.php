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
use Exception;
use Klein\AbstractResponse;
use LogicException;
use Paulus\DataCollection\ImmutableDataCollection;
use Paulus\Exception\AlreadyPreparedException;
use Paulus\Exception\Http\ApiExceptionInterface;
use Paulus\Exception\Http\ApiVerboseExceptionInterface;
use Paulus\FileLoader\RouteLoader;
use Paulus\FileLoader\RouteLoaderFactory;
use Paulus\Response\ApiResponse;

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
     * The Service Locator used throughout the app
     *
     * @var ServiceLocator
     * @access protected
     */
    protected $locator;

    /**
     * Whether the application been prepared or not
     *
     * @var boolean
     * @access protected
     */
    protected $prepared = false;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param Router $router            The Router instance to use for HTTP routing
     * @param ServiceLocator $locator   The service locator/container for the app
     * @access public
     */
    public function __construct(Router $router = null, ServiceLocator $locator = null)
    {
        // First things first... get our init time
        $this->initStartTime();

        // Set our router with a context of this application instance
        $this->router = $router ?: new Router();

        // Bind our Paulus app
        $this->router->bindPaulusApp($this);

        // Setup our service locator
        $this->locator = $locator ?: new ServiceLocator();

        // Setup our exception handler
        $this->setupExceptionHandler();
    }

    /**
     * Initialize our start time, only
     * allowing it to be set once
     *
     * @access public
     * @return Paulus
     */
    public function initStartTime()
    {
        // Don't allow this to be set more than once
        if (null === $this->start_time) {
            $this->start_time = microtime(true);
        }

        return $this;
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
     * Get the service locator instance
     *
     * @access public
     * @return ServiceLocator
     */
    public function locator()
    {
        return $this->locator;
    }

    /**
     * Setup our exception handler
     *
     * Setup our global exception handler for Paulus,
     * try and setup our controller's exception handler,
     * and fall back to our generic exception handler
     *
     * @access protected
     * @return Paulus
     */
    protected function setupExceptionHandler()
    {
        // Setup the global exception handler
        set_exception_handler([$this, 'handleException']);

        // Register an error handler through our router's catcher
        $this->router->onError(
            function ($router, $message, $class, Exception $exception) {

                // Check if we have a callable handler in our controller
                $callable = $router->getControllerExceptionHandler();

                if ($callable !== false) {
                    // Call and return our controller's exception handler
                    return call_user_func($callable, $exception);
                }

                return $this->handleException($exception);
            }
        );

        return $this;
    }

    /**
     * Handle an exception
     *
     * Handles any exception thrown in the application,
     * so we always respond RESTfully
     *
     * @param Exception $exception
     * @access public
     * @return Paulus
     */
    public function handleException(Exception $exception)
    {
        // Handle our RESTful exceptions
        if ($exception instanceof ApiExceptionInterface) {

            $this->handleRestfulException($exception);
        } else {

            // Grab the response
            $response = $this->router->response();

            // Unlock the response and set its response code
            $response->unlock()->code(500);

            if ($response instanceof ApiResponse) {

                // Set our slug and message
                $response
                    ->setStatusSlug('EXCEPTION_THROWN')
                    ->setMessage($exception->getMessage());
            }

            // Send the response
            $response->send();
        }

        return $this;
    }

    /**
     * Handle exceptions implementing the ApiExceptionInterface
     *
     * @param ApiExceptionInterface $exception
     * @access public
     * @return Paulus
     */
    public function handleRestfulException(ApiExceptionInterface $exception)
    {
        // Grab the response
        $response = $this->router->response();

        // Unlock the response
        $response->unlock();

        // Set the response code of the response based on the exception's code
        $response->code($exception->getCode());

        if ($response instanceof ApiResponse) {

            // Set our slug and message
            $response
                ->setStatusSlug($exception->getSlug())
                ->setMessage($exception->getMessage());

            if ($exception instanceof ApiVerboseExceptionInterface) {
                $response->setMoreInfo($exception->getMoreInfo());
            }
        }

        // Send the response
        $response->send();

        return $this;
    }

    /**
     * Prepare the application to be run
     *
     * @param boolean $auto_load_routes Whether or not we should attempt to automatically load the route definitions
     * @param RouteLoader $route_loader The route loader to use when preparing routes
     * @access public
     * @return Paulus
     */
    public function prepare($auto_load_routes = false, RouteLoader $route_loader = null)
    {
        // Don't allow the preparing of an application more than once
        if ($this->prepared) {
            throw new AlreadyPreparedException();
        }

        if ($auto_load_routes) {
            // Try and build our RouteLoader instance by inferring the route directory
            $route_loader = $route_loader ?: RouteLoaderFactory::buildByDirectoryInferring($this->router);

            // Actually load our routes
            $route_loader->load();
        }

        // Setup our routes for handling simple errors
        $this->router->setupDefaultErrorRoutes();

        $this->prepared = true;

        return $this;
    }

    /**
     * Run the application
     *
     * Optionally pass in custom Request and Response instances
     * and define how the application should handle the output
     *
     * @param Request $request              The request object to give to each callback
     * @param AbstractResponse $response    The response object to give to each callback
     * @param boolean $send_response        Whether or not to "send" the response after the last route has been matched
     * @param int $capture                  Specify a DISPATCH_* constant to change the output capturing behavior
     * @access public
     * @return void|string
     */
    public function run(
        Request $request = null,
        AbstractResponse $response = null,
        $send_response = true,
        $capture = Router::DISPATCH_NO_CAPTURE
    ) {

        // Prepare the application if we haven't done so yet
        if (!$this->prepared) {
            $this->prepare();
        }

        return $this->router->dispatch(
            $request,
            $response,
            $send_response,
            $capture
        );
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

        return call_user_func_array([$this->router(), $method], $args);
    }
}
