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
     * The "more_info" map key to use for notifying of the allowed methods
     *
     * @type string
     */
    const MORE_INFO_ALLOWED_METHODS_KEY = 'possible_methods';


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


    /**
     * Methods
     */

    /**
     * Set the HTTP methods that are "allowed" or possible
     *
     * This helps communicate to the client what methods would be correct to use
     * when they receive this exception as an HTTP response
     *
     * @param array $allowed_methods
     * @return WrongMethod
     */
    public function setAllowedMethods(array $allowed_methods)
    {
        // Tell them of the possible methods
        $this->setMoreInfo([
            static::MORE_INFO_ALLOWED_METHODS_KEY => $allowed_methods,
        ]);

        return $this;
    }
}
