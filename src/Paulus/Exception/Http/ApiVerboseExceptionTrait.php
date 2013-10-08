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
 * ApiVerboseExceptionTrait
 *
 * Basic implementation of the ApiVerboseExceptionInterface
 *
 * @package Paulus\Exception\Http
 */
trait ApiVerboseExceptionTrait
{

    /**
     * Properties
     */

    /**
     * The more info object containing
     * verbose error meta information
     *
     * @var object
     * @access protected
     */
    protected $more_info;


    /**
     * Methods
     */

    /**
     * Get the "more info"
     *
     * @access public
     * @return object
     */
    public function getMoreInfo()
    {
        return $this->more_info;
    }

    /**
     * Set the "more info"
     *
     * @param array $more_info
     * @access public
     * @return ApiVerboseExceptionTrait
     */
    public function setMoreInfo(array $more_info)
    {
        $this->more_info = (object) $more_info;

        return $this;
    }
}
