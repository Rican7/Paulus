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

namespace Paulus\Exception\Http\Standard;

use OutOfBoundsException;
use Paulus\Exception\Http\ApiExceptionInterface;
use Paulus\Exception\Http\ApiExceptionTrait;

/**
 * NotFound
 *
 * Exception representing a standard HTTP 404 error
 *
 * @uses    OutOfBoundsException
 * @uses    ApiExceptionInterface
 * @package Paulus\Exception\Http\Standard
 */
class NotFound extends OutOfBoundsException implements ApiExceptionInterface
{

    /**
     * Traits
     */

    // Include our basic interface required functionality
    use ApiExceptionTrait;


    /**
     * Constants
     */

    /**
     * The default exception message
     *
     * @const string
     */
    const DEFAULT_MESSAGE = '';

    /**
     * The default exception code
     *
     * @const int
     */
    const DEFAULT_CODE = 404;


    /**
     * Properties
     */

    /**
     * The exception and response code
     *
     * @var int
     * @access protected
     */
    protected $code = self::DEFAULT_CODE;
}
