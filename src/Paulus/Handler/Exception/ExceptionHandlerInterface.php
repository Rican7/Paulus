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
use Throwable;

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
     * Handle an exception thrown in the application
     *
     * TODO: Change the `$exception` parameter to type-hint against `Throwable`
     * once PHP 5.x support is no longer necessary.
     *
     * @param Exception|Throwable $exception
     * @access public
     * @return boolean
     */
    public function handleException($exception);
}
