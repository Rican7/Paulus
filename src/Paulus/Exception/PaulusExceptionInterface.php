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

/**
 * PaulusExceptionInterface
 *
 * Exception interface that all Paulus exceptions should implement
 *
 * This is mostly for having a simple, common Interface class/namespace
 * that can be type-hinted/instance-checked against, therefore making it
 * easier to handle Paulus exceptions while still allowing the different
 * exception classes to properly extend the corresponding SPL Exception type
 *
 * @package Paulus
 */
interface PaulusExceptionInterface
{
}
