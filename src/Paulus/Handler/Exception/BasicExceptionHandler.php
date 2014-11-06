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

namespace Paulus\Handler\Exception;

use Exception;
use Paulus\Paulus;
use Paulus\Response\ApiResponse;
use Psr\Log\LoggerInterface;

/**
 * BasicExceptionHandler
 *
 * A basic exception handler
 *
 * @package Paulus\Handler\Exception
 */
class BasicExceptionHandler implements ExceptionHandlerInterface
{

    /**
     * Constants
     */

    /**
     * The format for the info message
     *
     * @const string
     */
    const INFO_MESSAGE_FORMAT = 'Exception being handled by the Paulus %s exception handler';


    /**
     * Properties
     */

    /**
     * The logger
     *
     * @var LoggerInterface
     * @access protected
     */
    protected $logger;

    /**
     * The Paulus application
     *
     * @var Paulus
     * @access protected
     */
    protected $application;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Paulus $application
     * @access public
     */
    public function __construct(LoggerInterface $logger, Paulus $application)
    {
        $this->logger = $logger;
        $this->application = $application;
    }

    /**
     * Handle an exception
     *
     * Handle an exception thrown in the application
     *
     * @param Exception $exception
     * @access public
     * @return boolean
     */
    public function handleException(Exception $exception)
    {
        $this->logInfoMessage();

        // Write to our log
        $this->logger->critical($exception->getMessage(), ['exception' => $exception]);

        // Grab the response
        $response = $this->application->router()->response();

        // If we haven't initialized a response yet...
        if ($response === null) {
            $response = $this->application->getDefaultResponse();
        }

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

        return true;
    }

    /**
     * Log our informational message
     *
     * @access protected
     * @return void
     */
    protected function logInfoMessage()
    {
        // Write to our log
        $this->logger->info(
            sprintf(static::INFO_MESSAGE_FORMAT, get_called_class())
        );
    }
}
