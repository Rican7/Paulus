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

/**
 * UnableToInferRouteDirectoryException
 *
 * Exception for when an attempt to infer the route directory fails
 *
 * @uses    RouteAutoLoadFailureException
 * @uses    PaulusExceptionInterface
 * @package Paulus
 */
class UnableToInferRouteDirectoryException extends RouteAutoLoadFailureException implements PaulusExceptionInterface
{

    /**
     * Constants
     */

    /**
     * The default exception message
     *
     * @const string
     */
    const DEFAULT_MESSAGE = 'Unable to infer route directory.
        Please provide a RouteLoader instance
        with a valid path to the `prepare()` method.';


    /**
     * Properties
     */

    /**
     * The exception message
     *
     * @var string
     * @access protected
     */
    protected $message = self::DEFAULT_MESSAGE;
}
