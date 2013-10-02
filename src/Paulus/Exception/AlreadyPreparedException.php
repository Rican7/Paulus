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

namespace Paulus\Exception;

use LogicException;

/**
 * AlreadyPreparedException
 *
 * Exception used for when an attempt is made to "prepare"
 * a Paulus app that has already been prepared
 *
 * @uses    LogicException
 * @uses    PaulusExceptionInterface
 * @package Paulus
 */
class AlreadyPreparedException extends LogicException implements PaulusExceptionInterface
{

    /**
     * Properties
     */

    protected $message = 'Paulus has already been prepared';
}
