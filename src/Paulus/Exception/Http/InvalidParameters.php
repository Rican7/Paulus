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

use Paulus\Exception\Http\Standard\BadRequest;

/**
 * InvalidParameters
 *
 * Exception representing a request that failed
 * to provide whole or valid parameters
 *
 * @uses    BadRequest
 * @package Paulus\Exception\Http
 */
class InvalidParameters extends BadRequest implements ApiVerboseExceptionInterface
{

    /**
     * Traits
     */

    // Include our basic interface required functionality
    use ApiVerboseExceptionTrait;


    /**
     * Properties
     */

    /**
     * The exception slug
     *
     * @var string
     * @access protected
     */
    protected $slug = 'INVALID_PARAMETERS';

    /**
     * The exception message
     *
     * @var string
     * @access protected
     */
    protected $message = 'The posted data did not pass validation';
}
