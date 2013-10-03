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

/**
 * ApiVerboseExceptionInterface
 *
 * Exception interface for exceptions that support a
 * verbose and informative set of response properties
 * compatible with the API meta attributes used in an
 * API response
 *
 * @uses    ApiExceptionInterface
 * @package Paulus\Exception\Http
 */
interface ApiVerboseExceptionInterface extends ApiExceptionInterface
{

    /**
     * Get the "more info"
     *
     * @access public
     * @return object
     */
    public function getMoreInfo();

    /**
     * Set the "more info"
     *
     * @param array $more_info
     * @access public
     * @return ApiVerboseExceptionInterface
     */
    public function setMoreInfo(array $more_info);
}
