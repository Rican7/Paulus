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

namespace Paulus\Controller;

use Exception;

/**
 * ControllerInterface
 *
 * @package Paulus\Controller
 */
interface ControllerInterface
{

    /**
     * Handle the result of the route callback called
     * through the current controller
     *
     * @param mixed $result_data    The data to be evaluated and/or filtered
     * @access public
     * @return ControllerInterface
     */
    public function handleResult($result_data);

    /**
     * Handle a exception thrown during the callback
     * execution of the current controller
     *
     * @param Exception $e  The exception object that was thrown
     * @access public
     * @return ControllerInterface
     */
    public function handleException(Exception $e);
}
