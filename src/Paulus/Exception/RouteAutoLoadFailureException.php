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

namespace Paulus\Exception;

use UnexpectedValueException;

/**
 * RouteAutoLoadFailureException
 *
 * Exception for when an attempt to auto-load routes fails
 *
 * @uses    UnexpectedValueException
 * @uses    PaulusExceptionInterface
 * @package Paulus
 */
class RouteAutoLoadFailureException extends UnexpectedValueException implements PaulusExceptionInterface
{

    /**
     * Properties
     */

    protected $message = 'Unable to load routes automatically';
}
