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

use Exception;
use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * BasicLogger
 *
 * @uses LoggerInterface
 * @package Paulus\Logger
 */
class BasicLogger extends AbstractLogger implements LoggerInterface
{

    /**
     * Format the message based on its level and context
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @access protected
     * @return string
     */
    protected function formatMessage($level, $message, array $context = [])
    {
        $formatted = sprintf('PHP Log - Level "%s": %s', strtoupper($level), $message);

        // If we have an exception...
        if (isset($context['exception']) && $context['exception'] instanceof Exception) {
            // Add to the formatted string
            $formatted .= sprintf(
                PHP_EOL.PHP_EOL. 'Exception "%s" (code %d) thrown with trace:"%s"',
                get_class($context['exception']),
                $context['exception']->getCode(),
                PHP_EOL.PHP_EOL. $context['exception']->getTraceAsString()
            );
        }

        return $formatted;
    }

    /**
     * Actually write the log
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @access public
     * @return BasicLogger
     */
    protected function writeLog($level, $message, array $context = [])
    {
        // Use the system's "error_log" function
        error_log(
            $this->formatMessage($level, $message, $context)
        );

        return $this;
    }

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
            case LogLevel::EMERGENCY:
            case LogLevel::ALERT:
            case LogLevel::CRITICAL:
            case LogLevel::ERROR:
            case LogLevel::WARNING:
            case LogLevel::NOTICE:
            case LogLevel::INFO:
            case LogLevel::DEBUG:
                $this->writeLog($level, $message, $context);
                break;
            default:
                throw new InvalidArgumentException('Invalid log level: '. $level);
                break;
        }
    }
}
