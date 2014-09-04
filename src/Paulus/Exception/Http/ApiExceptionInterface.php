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
use Paulus\Exception\PaulusExceptionInterface;

/**
 * ApiExceptionInterface
 *
 * Exception interface for exceptions that support
 * the API meta attributes used in an API response
 *
 * @uses    Paulus\Exception\PaulusExceptionInterface
 * @package Paulus\Exception\Http
 */
interface ApiExceptionInterface extends PaulusExceptionInterface
{

    /**
     * Static creation method designed to allow for easier fall-back to default
     * values than the default exception constructor
     *
     * @param string $message
     * @param int $code
     * @param Exception $previous
     * @static
     * @access public
     * @return ApiExceptionInterface
     */
    public static function create($message = null, $code = null, Exception $previous = null);

    /**
     * Get the default message for this type of exception
     *
     * @static
     * @access public
     * @return string
     */
    public static function getDefaultMessage();

    /**
     * Get the default code for this type of exception
     *
     * @static
     * @access public
     * @return int
     */
    public static function getDefaultCode();

    /**
     * Get the slug
     *
     * @access public
     * @return string
     */
    public function getSlug();
}
