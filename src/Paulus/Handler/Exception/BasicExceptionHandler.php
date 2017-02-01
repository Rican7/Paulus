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

use Klein\AbstractResponse;
use Paulus\Response\ApiResponse;
use Psr\Log\LoggerInterface;

/**
 * BasicExceptionHandler
 *
 * A basic exception handler
 *
 * @package Paulus\Handler\Exception
 */
class BasicExceptionHandler implements ExceptionResponseHandlerInterface
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
     * The response to send
     *
     * @var AbstractResponse
     * @access protected
     */
    protected $response;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param AbstractResponse $response
     * @access public
     */
    public function __construct(LoggerInterface $logger, AbstractResponse $response)
    {
        $this->logger = $logger;
        $this->response = $response;
    }

    /**
     * Get the response
     *
     * @access public
     * @return AbstractResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set the response
     *
     * @param AbstractResponse $response
     * @access public
     * @return BasicExceptionHandler
     */
    public function setResponse(AbstractResponse $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function handleException($exception)
    {
        $this->logInfoMessage();

        // Write to our log
        $this->logger->critical($exception->getMessage(), ['exception' => $exception]);

        // Send our response
        $this->response->send();

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
