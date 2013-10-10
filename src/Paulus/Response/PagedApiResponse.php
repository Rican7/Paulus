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
 * PagedApiResponse
 *
 * @uses ApiResponse
 * @package Paulus\Response
 */
class PagedApiResponse extends ApiResponse
{

    /**
     * Properties
     */

    /**
     * The current page responding with
     *
     * @var int
     * @access protected
     */
    protected $page = 1;

    /**
     * The number of results per page
     *
     * @var int
     * @access protected
     */
    protected $per_page = 10;

    /**
     * Whether or not the response has a "next" page or not
     *
     * @var boolean
     * @access protected
     */
    protected $has_next_page = false;


    /**
     * Methods
     */

    /**
     * Get the page number
     *
     * @access public
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set the page number
     *
     * @param int $page
     * @access public
     * @return PagedApiResponse
     */
    public function setPage($page)
    {
        $this->page = (int) $page;

        $this->updateBody();

        return $this;
    }

    /**
     * Get the per page count
     *
     * @access public
     * @return int
     */
    public function getPerPage()
    {
        return $this->per_page;
    }

    /**
     * Set the per page count
     *
     * @param int $per_page
     * @access public
     * @return PagedApiResponse
     */
    public function setPerPage($per_page)
    {
        $this->per_page = (int) $per_page;

        $this->updateBody();

        return $this;
    }

    /**
     * Get the has next page boolean
     *
     * @access public
     * @return boolean
     */
    public function getHasNextPage()
    {
        return $this->has_next_page;
    }

    /**
     * Set the has next page boolean
     *
     * @param boolean $has_next_page
     * @access public
     * @return PagedApiResponse
     */
    public function setHasNextPage($has_next_page)
    {
        $this->has_next_page = (bool) $has_next_page;

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
            'page' => (int) $this->getPage(),
            'per_page' => (int) $this->getPerPage(),
            // 'resources' => (object) [], // TODO
        ];

        return $formatted;
    }
}
