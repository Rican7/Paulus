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

namespace Paulus\Handler\Error;

use ErrorException;

/**
 * BasicErrorHandler
 *
 * A basic error handler
 *
 * @package Paulus\Handler\Error
 */
class BasicErrorHandler implements ErrorHandlerInterface
{

    /**
     * Handle an error
     *
     * Handle an error raised in the application
     *
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $context
     * @access public
     * @return boolean
     */
    public function handleError($level, $message, $file = null, $line = null, array $context = null)
    {
        // If the error level has been disabled in our error reporting configuration
        if (!($level & error_reporting())) {
            // Don't handle it
            return false;
        }

        throw new ErrorException($message, 0, $level, $file, $line);
    }
}
