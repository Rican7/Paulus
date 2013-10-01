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

use OverflowException;

/**
 * DuplicateServiceException
 *
 * Exception used for when a service is attempted
 * to be registered that already exists
 *
 * @uses    OverflowException
 * @uses    PaulusExceptionInterface
 * @package Paulus
 */
class DuplicateServiceException extends OverflowException implements PaulusExceptionInterface
{
}
