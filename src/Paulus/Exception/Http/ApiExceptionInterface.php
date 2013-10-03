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
     * Get the slug
     *
     * @access public
     * @return string
     */
    public function getSlug();
}
