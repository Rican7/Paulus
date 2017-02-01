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

use Exception;
use Throwable;

/**
 * ApiExceptionTrait
 *
 * Basic implementation of the ApiExceptionInterface
 *
 * @package Paulus\Exception\Http
 */
trait ApiExceptionTrait
{

    /**
     * Properties
     */

    /**
     * A string designed to refer to
     * the type of error being thrown
     *
     * @var string
     * @access protected
     */
    protected $slug;


    /**
     * Methods
     */

    /**
     * Static creation method designed to allow for easier fall-back to default
     * values than the default exception constructor
     *
     * TODO: Change the `$previous` parameter to type-hint against `Throwable`
     * once PHP 5.x support is no longer necessary.
     *
     * @param string $message
     * @param int $code
     * @param Exception|Throwable $previous
     * @static
     * @access public
     * @return static
     */
    public static function create($message = null, $code = null, $previous = null)
    {
        $message = (null !== $message) ? $message : static::getDefaultMessage();
        $code = (null !== $code) ? $code : static::getDefaultCode();

        return new static($message, $code, $previous);
    }

    /**
     * Get the default message for this type of exception
     *
     * @static
     * @access public
     * @return string
     */
    public static function getDefaultMessage()
    {
        $default_message_constant = 'static::DEFAULT_MESSAGE';

        if (defined($default_message_constant)) {
            return (string) constant($default_message_constant);
        } else {
            trigger_error('Class constant DEFAULT_MESSAGE not defined', E_USER_NOTICE);
        }

        return '';
    }

    /**
     * Get the default code for this type of exception
     *
     * @static
     * @access public
     * @return int
     */
    public static function getDefaultCode()
    {
        $default_code_constant = 'static::DEFAULT_CODE';

        if (defined($default_code_constant)) {
            return (int) constant($default_code_constant);
        } else {
            trigger_error('Class constant DEFAULT_CODE not defined', E_USER_NOTICE);
        }

        return 0;
    }

    /**
     * Get the slug
     *
     * @access public
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
