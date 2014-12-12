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
use Klein\Response;
use LogicException;
use Paulus\Exception\AlreadyPreparedException;
use Paulus\Exception\Http\EndpointNotFound;
use Paulus\Exception\Http\WrongMethod;
use Paulus\FileLoader\RouteLoader;
use Paulus\FileLoader\RouteLoaderFactory;
use Paulus\Handler\Error\ErrorHandlerInterface;
use Paulus\Handler\Error\LevelBasedErrorHandler;
use Paulus\Handler\Exception\ExceptionHandlerInterface;
use Paulus\Handler\Exception\RestfulExceptionHandler;
use Paulus\Logger\BasicLogger;
use Paulus\Request\Request;
use Paulus\Response\ApiResponse;
use Psr\Log\LoggerInterface;

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
     * Constants
     */

    /**
     * The key used in our service locator
     * for our logger instance
     *
     * @const string
     */
    const LOGGER_KEY = 'logger';

    /**
     * The default response class to use for
     * in case a response hasn't been created
     * or an instance hasn't been set for the
     * "default_response" property
     *
     * @const string
     */
    const DEFAULT_RESPONSE_CLASS = '\Klein\Response';


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
     * The default response instance to use in
     * case a response hasn't been created
     * yet and an exception is thrown
     *
     * @var AbstractResponse
     * @access protected
     */
    protected $default_response;

    /**
     * The exception handler used to handle any application exceptions
     *
     * @var ExceptionHandlerInterface
     * @access protected
     */
    protected $exception_handler;

    /**
     * The error handler used to handle any low-level application errors
     *
     * @var ErrorHandlerInterface
     * @access protected
     */
    protected $error_handler;

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
    public function __construct(Router $router = null, ServiceLocator $locator = null, LoggerInterface $logger = null)
    {
        // First things first... get our init time
        $this->initStartTime();

        // Set our router with a context of this application instance
        $this->router = $router ?: new Router();

        // Bind our Paulus app
        $this->router->bindPaulusApp($this);

        // Setup our service locator
        $this->locator = $locator ?: new ServiceLocator();

        // Setup our logger
        $this->locator[static::LOGGER_KEY] = $logger ?: new BasicLogger();

        // Setup our default response
        $response_class = static::DEFAULT_RESPONSE_CLASS;
        $default_response = new $response_class();
        $this->setDefaultResponse($default_response);

        // Setup our exception handler
        $this->setExceptionHandler(
            new RestfulExceptionHandler($this->locator[static::LOGGER_KEY], $default_response)
        );
        $this->setupRouterExceptionHandler();

        // Setup our error handler
        $this->setErrorHandler(new LevelBasedErrorHandler());

        // Setup our HTTP error handler
        $this->setupRouterHttpErrorHandler();

        // Setup our after dispatch handler
        $this->setupAfterDispatchHandler();

        // Write to our log
        $this->logger()->debug('Paulus application constructed at start time: '. $this->start_time);
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
     * Get the application's logger instance
     *
     * @access public
     * @return LoggerInterface
     */
    public function logger()
    {
        return $this->locator[static::LOGGER_KEY];
    }

    /**
     * Get the default response object
     *
     * @access public
     * @return AbstractResponse
     */
    public function getDefaultResponse()
    {
        return $this->default_response;
    }

    /**
     * Set the default response object
     *
     * @param AbstractResponse $default_response description
     * @access public
     * @return Paulus
     */
    public function setDefaultResponse(AbstractResponse $default_response)
    {
        $this->default_response = $default_response;

        return $this;
    }

    /**
     * Get the exception_handler
     *
     * @access public
     * @return ExceptionHandlerInterface
     */
    public function getExceptionHandler()
    {
        return $this->exception_handler;
    }

    /**
     * Set the exception_handler
     *
     * @param ExceptionHandlerInterface $exception_handler
     * @access public
     * @return Paulus
     */
    public function setExceptionHandler(ExceptionHandlerInterface $exception_handler)
    {
        $this->exception_handler = $exception_handler;

        // Setup the global exception handler
        set_exception_handler([$this->exception_handler, 'handleException']);

        return $this;
    }

    /**
     * Get the error_handler
     *
     * @access public
     * @return ErrorHandlerInterface
     */
    public function getErrorHandler()
    {
        return $this->error_handler;
    }

    /**
     * Set the error_handler
     *
     * @param ErrorHandlerInterface $error_handler
     * @param int $error_types
     * @access public
     * @return Paulus
     */
    public function setErrorHandler(ErrorHandlerInterface $error_handler, $error_types = null)
    {
        $this->error_handler = $error_handler;

        // Setup the global error handler
        $callable = [$this->error_handler, 'handleError'];

        if (null !== $error_types) {
            set_error_handler($callable, $error_types);
        } else {
            set_error_handler($callable);
        }

        return $this;
    }

    /**
     * Setup our router's exception handler
     *
     * Setup our router's exception handler by first trying
     * to setup our controller's exception handler, and
     * falling back to our generic exception handler
     *
     * @access protected
     * @return Paulus
     */
    protected function setupRouterExceptionHandler()
    {
        // Register an error handler through our router's catcher
        $this->router->onError(
            function ($router, $message, $class, Exception $exception) {

                // Check if we have a callable handler in our controller
                $callable = $router->getControllerExceptionHandler();

                if ($callable !== false) {
                    // Call and return our controller's exception handler
                    return call_user_func($callable, $exception);
                }

                return $this->exception_handler->handleException($exception);
            }
        );

        return $this;
    }

    /**
     * Setup our router's HTTP error handler
     *
     * This uses our router's http error callback registration method to assign
     * a basic, default handler to execute when an HTTP error occurs
     *
     * @return Paulus
     */
    protected function setupRouterHttpErrorHandler()
    {
        $this->router->onHttpError(
            function ($code, $router, $matched, $methods_matched, $http_exception) {
                switch ($code) {
                    case 404:
                        throw EndpointNotFound::create(null, null, $http_exception);
                        break;
                    case 405:
                        // Don't error out on an OPTIONS request
                        if (!$router->request()->method('OPTIONS')) {
                            $exception = WrongMethod::create(null, null, $http_exception);

                            // Tell them of the possible methods
                            $exception->setMoreInfo(
                                [
                                    'possible_methods' => $methods_matched,
                                ]
                            );

                            throw $exception;
                        }
                        break;
                    default:
                        throw $http_exception;
                }
            }
        );

        return $this;
    }

    /**
     * Setup our after dispatch handler
     *
     * @access protected
     * @return Paulus
     */
    protected function setupAfterDispatchHandler()
    {
        // Register an error handler through our router's catcher
        $this->router->afterDispatch(
            function ($router) {
                $request = $router->request();
                $response = $router->response();

                if ($response instanceof ApiResponse) {
                    // Request was an OPTIONS request and more info is empty
                    if ($request->method('OPTIONS') && null === $response->getMoreInfo()) {
                        // Get our allowed headers
                        $allowed = $response->headers()->get('Allow');

                        // Tell them of the possible methods
                        $response->unlock();
                        $response->setMoreInfo(
                            [
                                'possible_methods' => explode(', ', $allowed)
                            ]
                        );
                    }
                }
            }
        );

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

        $this->prepared = true;

        // Write to our log
        $this->logger()->debug('Paulus application prepared');

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

        // Write to our log
        $this->logger()->debug('Paulus application running!');

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
