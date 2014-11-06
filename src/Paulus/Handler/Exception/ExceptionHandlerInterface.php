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

/**
 * ExceptionHandlerInterface
 *
 * An interface definition for application exception handlers
 *
 * @package Paulus\Handler\Exception
 */
interface ExceptionHandlerInterface
{

    /**
     * Handle an exception
     *
     * Handle an exception thrown in the application
     *
     * @param Exception $exception
     * @access public
     * @return boolean
     */
    public function handleException(Exception $exception);
}
