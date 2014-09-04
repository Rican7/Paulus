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

use Paulus\Exception\Http\Standard\Unauthorized;

/**
 * UnauthorizedAccess
 *
 * Exception representing a lack of authorization in
 * the request when accessing an object/resource
 *
 * @uses    Unauthorized
 * @package Paulus\Exception\Http
 */
class UnauthorizedAccess extends Unauthorized
{

    /**
     * Constants
     */

    /**
     * The default exception message
     *
     * @const string
     */
    const DEFAULT_MESSAGE = 'You are not authorized to access or modify this';


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
