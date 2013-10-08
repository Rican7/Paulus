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

namespace Paulus\Controller;

use Exception;
use Klein\AbstractResponse;
use Klein\ServiceProvider;
use Paulus\Exception\Http\InvalidParameters;
use Paulus\Exception\Http\ObjectNotFound;
use Paulus\Paulus;
use Paulus\Request;

/**
 * AbstractController
 *
 * @uses    ControllerInterface
 * @abstract
 * @package Paulus\Controller
 */
abstract class AbstractController implements ControllerInterface
{

    /**
     * Properties
     */

    /**
     * The HTTP Request object
     *
     * @var Paulus\Request
     * @access protected
     */
    protected $request;

    /**
     * The HTTP Response object
     *
     * @var Klein\AbstractResponse
     * @access protected
     */
    protected $response;

    /**
     * The HTTP Request/Response service
     * provider object
     *
     * @var Klein\ServiceProvider
     * @access protected
     */
    protected $service;

    /**
     * The current Paulus application instance
     *
     * @var Paulus\Paulus
     * @access protected
     */
    protected $app;

    /**
     * The HTTP Router instance
     *
     * @var Paulus\Router
     * @access protected
     */
    protected $router;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param Request $request
     * @param AbstractResponse $response
     * @param ServiceProvider $service
     * @param Paulus $app
     * @param Router $router
     * @access public
     */
    public function __construct(
        Request $request,
        AbstractResponse $response,
        ServiceProvider $service,
        Paulus $app,
        Router $router
    ) {
        // Assignment city
        $this->request  = $request;
        $this->response = $response;
        $this->service  = $service;
        $this->app      = $app;
        $this->router   = $router;
    }

    /**
     * Handle the result of the route callback called
     * through the current controller
     *
     * @param mixed $result_data    The data to be evaluated and/or filtered
     * @access public
     * @return ControllerInterface
     */
    public function handleResult($result_data)
    {
        // If the response is null.. we didn't get back a result
        if (null === $result_data) {
            throw new ObjectNotFound();
        }

        // True response
        if ($result_data === true) {
            // True case WITHOUT any returned data

        } elseif ($result_data === false) {
            // Throw an exception
            throw new InvalidParameters();

        } else {
            if ($this->response instanceof ApiResponse) {
                // Prepare our data for response
                $this->response->setData($result_data);
            }
        }
    }

    /**
     * Handle a exception thrown during the callback
     * execution of the current controller
     *
     * @param Exception $e  The exception object that was thrown
     * @access public
     * @return ControllerInterface
     */
    public function handleException(Exception $e)
    {
        // Let's turn PDO database exceptions into 502's
        if ($e instanceof \PDOException) {
            throw new BadGateway(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Check if a given class is a child of this class
     *
     * @param mixed $class
     * @static
     * @access public
     * @return boolean
     */
    public static function isChildClass($class)
    {
        // Get all the parents of the given class
        $parents = class_parents($class);

        // Is the AbstractController one of its parents?
        return (isset($parents[__CLASS__]));
    }
}
