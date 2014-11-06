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

namespace Paulus\Handler\Exception;

use Klein\AbstractResponse;

/**
 * ExceptionResponseHandlerInterface
 *
 * An interface definition for application exception handlers that
 * use a response object in some way, usually to send a canned response
 *
 * @package Paulus\Handler\Exception
 */
interface ExceptionResponseHandlerInterface extends ExceptionHandlerInterface
{

    /**
     * Get the response
     *
     * @access public
     * @return AbstractResponse
     */
    public function getResponse();

    /**
     * Set the response
     *
     * @param AbstractResponse $response
     * @access public
     * @return ExceptionResponseHandlerInterface
     */
    public function setResponse(AbstractResponse $response);
}
