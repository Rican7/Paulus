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
use Paulus\Response\ApiResponse;

/**
 * InformativeExceptionHandler
 *
 * An exception handler that is more informative
 *
 * @package Paulus\Handler\Exception
 */
class InformativeExceptionHandler extends BasicExceptionHandler
{

    /**
     * Constants
     */

    /**
     * The default response code to prepare
     *
     * @const int
     */
    const DEFAULT_RESPONSE_CODE = 500;

    /**
     * The default slug to include in the response
     *
     * @const string
     */
    const DEFAULT_SLUG = 'EXCEPTION_THROWN';

    /**
     * The exception class name key to use in the more info array
     *
     * @const string
     */
    const KEY_EXCEPTION_CLASS = 'exception_class';

    /**
     * The exception stack trace key to use in the more info array
     *
     * @const string
     */
    const KEY_STACK_TRACE = 'stack_trace';


    /**
     * Methods
     */

    /**
     * Handle an exception thrown in the application
     *
     * @param Exception $exception
     * @access public
     * @return boolean
     */
    public function handleException(Exception $exception)
    {
        // Prepare our error response
        $this->prepareErrorResponse(
            null,
            null,
            $exception->getMessage(),
            [
                static::KEY_EXCEPTION_CLASS => get_class($exception),
                static::KEY_STACK_TRACE => $exception->getTrace(),
            ]
        );

        return parent::handleException($exception);
    }

    /**
     * Prepare our error response
     *
     * @param int $code
     * @param string $slug
     * @param string $message
     * @param array|object $more_info
     * @return void
     */
    protected function prepareErrorResponse(
        $code = self::DEFAULT_RESPONSE_CODE,
        $slug = self::DEFAULT_SLUG,
        $message = null,
        $more_info = null
    ) {
        // Ensure our defaults are set
        $code = $code ?: static::DEFAULT_RESPONSE_CODE;
        $slug = $slug ?: static::DEFAULT_SLUG;

        // Grab the response
        $response = $this->response;

        // Unlock the response and set its response code
        $response->unlock()->code($code);

        if ($response instanceof ApiResponse) {

            // Set our slug and message
            $response
                ->setStatusSlug($slug)
                ->setMessage($message);

            if (null !== $more_info) {
                $response->setMoreInfo($more_info);
            }
        }

        return $response;
    }
}
