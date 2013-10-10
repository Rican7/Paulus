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

namespace Paulus\Response;

/**
 * IdLimitedApiResponse
 *
 * @uses ApiResponse
 * @package Paulus\Response
 */
class IdLimitedApiResponse extends ApiResponse
{

    /**
     * Properties
     */

    /**
     * The number of results to return
     *
     * @var int
     * @access protected
     */
    protected $count = 10;

    /**
     * The minimum ID to return when fetching
     * multiple results, for limiting the set
     *
     * @var int
     * @access protected
     */
    protected $min_id;

    /**
     * The maximum ID to return when fetching
     * multiple results, for limiting the set
     *
     * @var int
     * @access protected
     */
    protected $max_id;


    /**
     * Methods
     */

    /**
     * Get the count
     *
     * @access public
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set the count
     *
     * @param int $count
     * @access public
     * @return IdLimitedApiResponse
     */
    public function setCount($count)
    {
        $this->count = (int) $count;

        $this->updateBody();

        return $this;
    }

    /**
     * Get the min id number
     *
     * @access public
     * @return int
     */
    public function getMinId()
    {
        return $this->min_id;
    }

    /**
     * Set the min id number
     *
     * @param int $min_id
     * @access public
     * @return IdLimitedApiResponse
     */
    public function setMinId($min_id)
    {
        $this->min_id = (int) $min_id;

        $this->updateBody();

        return $this;
    }

    /**
     * Get the max id number
     *
     * @access public
     * @return int
     */
    public function getMaxId()
    {
        return $this->max_id;
    }

    /**
     * Set the max id number
     *
     * @param int $max_id
     * @access public
     * @return IdLimitedApiResponse
     */
    public function setMaxId($max_id)
    {
        $this->max_id = (int) $max_id;

        $this->updateBody();

        return $this;
    }

    /**
     * Get the response data in a formatted structure
     *
     * @access protected
     * @return object
     */
    protected function getFormattedResponseData()
    {
        // Call our parent and pre-populate all the other fields
        $formatted = parent::getFormattedResponseData();

        // Create our paging structure
        $formatted->paging = (object) [
            'count' => (int) $this->getCount(),
            // 'resources' => (object) [], // TODO
        ];

        // Only include the min_id if one was set
        if (null !== $this->getMinId()) {
            $formatted->paging->min_id = (int) $this->getMinId();
        }

        // Only include the max_id if one was set
        if (null !== $this->getMaxId()) {
            $formatted->paging->max_id = (int) $this->getMaxId();
        }

        return $formatted;
    }
}
