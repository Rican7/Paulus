<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful services
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2013 Trevor Suarez
 * @link        https://github.com/Rican7/Paulus
 * @version     2.0.0
 */

namespace Paulus\Logger;

use Psr\Log\LogLevel;

/**
 * UnitTestLogger
 *
 * @uses BasicLogger
 * @package Paulus\Logger
 */
class UnitTestLogger extends BasicLogger
{

    /**
     * Log with an arbitrary level
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @access public
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        // Handle or thrown an exception
        switch ($level) {
            case LogLevel::INFO:
            case LogLevel::DEBUG:
                // no-op
                return;
                break;
            default:
                return parent::log($level, $message, $context);
                break;
        }
    }
}
