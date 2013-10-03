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

use Paulus\Exception\Http\Standard\NotFound;

/**
 * EndpointNotFound
 *
 * Exception representing an inability to
 * find the endpoint that was requested
 *
 * @uses    NotFound
 * @package Paulus\Exception\Http
 */
class EndpointNotFound extends NotFound
{

    /**
     * The exception message
     *
     * @var string
     * @access protected
     */
    protected $message = 'Unable to find the endpoint you requested';
}
