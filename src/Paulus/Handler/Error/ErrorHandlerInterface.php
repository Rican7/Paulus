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

/**
 * ErrorHandlerInterface
 *
 * An interface definition for application error handlers
 *
 * @package Paulus\Handler\Error
 */
interface ErrorHandlerInterface
{

    /**
     * Handle an error raised in the application
     *
     * @link http://php.net/manual/en/function.set-error-handler.php#refsect1-function.set-error-handler-parameters
     * @param int $level        The level of the error raised
     * @param string $message   The error message
     * @param string $file      The filename that the error was raised in
     * @param int $line         The line number of the error's origin
     * @param array $context    An array containing all of the variables in scope present at error time
     * @access public
     * @return boolean
     */
    public function handleError($level, $message, $file = null, $line = null, array $context = null);
}
