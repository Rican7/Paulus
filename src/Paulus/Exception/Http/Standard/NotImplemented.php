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

use RuntimeException;
use Paulus\Exception\Http\ApiExceptionInterface;

/**
 * NotImplemented
 *
 * Exception representing a standard HTTP 501 error
 *
 * @uses    RuntimeException
 * @uses    ApiExceptionInterface
 * @package Paulus\Exception\Http\Standard
 */
class NotImplemented extends RuntimeException implements ApiExceptionInterface
{

    /**
     * Traits
     */

    // Include our basic interface required functionality
    use ApiExceptionTrait;


    /**
     * Properties
     */

    /**
     * The exception and response code
     *
     * @var int
     * @access protected
     */
    protected $code = 501;

    /**
     * The exception slug
     *
     * @var string
     * @access protected
     */
    protected $slug = null; // Leave null to use standard HTTP status slug
}
