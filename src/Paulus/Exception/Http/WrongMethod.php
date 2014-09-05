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

namespace Paulus\Exception\Http;

use Paulus\Exception\Http\Standard\MethodNotAllowed;

/**
 * WrongMethod
 *
 * Exception representing an incorrect method
 * was used in the request on an endpoint
 *
 * @uses    MethodNotAllowed
 * @package Paulus\Exception\Http
 */
class WrongMethod extends MethodNotAllowed implements ApiVerboseExceptionInterface
{

    /**
     * Traits
     */

    // Include our basic interface required functionality
    use ApiVerboseExceptionTrait;


    /**
     * Constants
     */

    /**
     * The default exception message
     *
     * @const string
     */
    const DEFAULT_MESSAGE = 'The wrong method was called on this endpoint';


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
