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
 * ApiExceptionTrait
 *
 * Basic implementation of the ApiExceptionInterface
 *
 * @package Paulus\Exception\Http
 */
trait ApiExceptionTrait
{

    /**
     * Properties
     */

    /**
     * A string designed to refer to
     * the type of error being thrown
     *
     * @var string
     * @access protected
     */
    protected $slug;


    /**
     * Methods
     */

    /**
     * Get the slug
     *
     * @access public
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
