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
use Klein\AbstractResponse;
use Klein\HttpStatus;
use Paulus\Exception\Http\ApiExceptionInterface;
use Paulus\Exception\Http\ApiVerboseExceptionInterface;
use Paulus\Support\Inflector;
use Psr\Log\LoggerInterface;

/**
 * RestfulExceptionHandler
 *
 * A Restful exception handler
 *
 * @package Paulus\Handler\Exception
 */
class RestfulExceptionHandler extends InformativeExceptionHandler
{

    /**
     * Properties
     */

    /**
     * The delegate exception handler
     *
     * This delegate handler will be used to handle the
     * exceptions that this handler doesn't want to
     *
     * @type ExceptionHandlerInterface
     */
    private $delegate;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param AbstractResponse $response
     * @param ExceptionHandlerInterface $delegate
     * @access public
     */
    public function __construct(
        LoggerInterface $logger,
        AbstractResponse $response,
        ExceptionHandlerInterface $delegate = null
    ) {
        parent::__construct($logger, $response);

        $this->delegate = $delegate;
    }

    /**
     * Gets the value of delegate
     *
     * @access public
     * @return ExceptionHandlerInterface
     */
    public function getDelegate()
    {
        return $this->delegate;
    }

    /**
     * Sets the value of delegate
     *
     * @param ExceptionHandlerInterface $delegate
     * @access public
     * @return RestfulExceptionHandler
     */
    public function setDelegate(ExceptionHandlerInterface $delegate)
    {
        $this->delegate = $delegate;

        return $this;
    }

    /**
     * Handle an exception thrown in the application
     *
     * @param Exception $exception
     * @access public
     * @return boolean
     */
    public function handleException(Exception $exception)
    {
        // Handle our RESTful exceptions
        if ($exception instanceof ApiExceptionInterface) {
            return $this->handleRestfulException($exception);

        } elseif (null !== $this->delegate) {
            return $this->delegate->handleException($exception);

        } else {
            return parent::handleException($exception);
        }

        return true;
    }

    /**
     * Handle exceptions implementing the ApiExceptionInterface
     *
     * @param ApiExceptionInterface $exception
     * @access public
     * @return boolean
     */
    public function handleRestfulException(ApiExceptionInterface $exception)
    {
        $this->logInfoMessage();

        // Write to our log
        $this->logger->error($exception->getMessage(), ['exception' => $exception]);

        $more_info = null;

        if ($exception instanceof ApiVerboseExceptionInterface) {
            $more_info = $exception->getMoreInfo();
        }

        // Prepare our error response
        $this->prepareErrorResponse(
            $exception->getCode(),
            $this->getSlugFromException($exception),
            $exception->getMessage(),
            $more_info
        );

        // Send our response
        $this->response->send();

        return true;
    }

    /**
     * Get a slug from an API exception
     *
     * This will get a slug from an ApiExceptionInterface by attempting to first
     * grab the defined slug and then falling back to using the standard HTTP
     * status code's message
     *
     * @param ApiExceptionInterface $exception
     * @access protected
     * @return string
     */
    protected function getSlugFromException(ApiExceptionInterface $exception)
    {
        $slug = $exception->getSlug();

        if (empty($slug)) {
            $http_status_message = (new HttpStatus($exception->getCode()))->getMessage();

            if (!empty($http_status_message)) {
                $slug = Inflector::constantStringify($http_status_message);
            }
        }

        return $slug;
    }
}
