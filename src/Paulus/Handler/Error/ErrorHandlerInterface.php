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
    public function handleError($level, $message, $file = null, $line = null, array $context = null);
}
