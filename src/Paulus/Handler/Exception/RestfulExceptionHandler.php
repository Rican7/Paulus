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
use Paulus\Exception\Http\ApiExceptionInterface;
use Paulus\Exception\Http\ApiVerboseExceptionInterface;
use Paulus\Paulus;
use Paulus\Response\ApiResponse;
use Psr\Log\LoggerInterface;

/**
 * RestfulExceptionHandler
 *
 * A Restful exception handler
 *
 * @package Paulus\Handler\Exception
 */
class RestfulExceptionHandler extends BasicExceptionHandler
{

    /**
     * Methods
     */

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
        // Handle our RESTful exceptions
        if ($exception instanceof ApiExceptionInterface) {
            return $this->handleRestfulException($exception);
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

        // Grab the response
        $response = $this->application->router()->response();

        // If we haven't initialized a response yet...
        if ($response === null) {
            $response = $this->application->getDefaultResponse();
        }

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

        return true;
    }
}
