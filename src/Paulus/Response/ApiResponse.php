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

use Paulus\Support\Inflector;

/**
 * ApiResponse
 *
 * @uses    JsonResponse
 * @package Paulus\Response
 */
class ApiResponse extends JsonResponse
{

    /**
     * Properties
     */

    /**
     * The main response data
     *
     * @var object|array
     * @access protected
     */
    protected $data;

    /**
     * The status slug
     *
     * @var string
     * @access protected
     */
    protected $status_slug;

    /**
     * The human readable message
     * of the response
     *
     * @var string
     * @access protected
     */
    protected $message;

    /**
     * The more info object containing
     * error or meta info regarding the
     * transaction
     *
     * @var object
     * @access protected
     */
    protected $more_info;


    /**
     * Methods
     */

    /**
     * Get the data
     *
     * @access public
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data
     *
     * @param mixed $data
     * @access public
     * @return ApiResponse
     */
    public function setData($data)
    {
        $this->data = $data;

        $this->updateBody();

        return $this;
    }

    /**
     * Get the status slug
     *
     * Will automatically fall back to
     * the HTTP status message for the
     * current HTTP status code
     *
     * @access public
     * @return mixed
     */
    public function getStatusSlug()
    {
        if (empty($this->status_slug)) {
            return Inflector::constantStringify(
                $this->status()->getMessage()
            );
        }

        return $this->status_slug;
    }

    /**
     * Set the status slug
     *
     * @param string $status_slug
     * @access public
     * @return ApiResponse
     */
    public function setStatusSlug($status_slug)
    {
        $this->status_slug = (string) $status_slug;

        $this->updateBody();

        return $this;
    }

    /**
     * Get the message
     *
     * @access public
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the message
     *
     * @param string $message
     * @access public
     * @return ApiResponse
     */
    public function setMessage($message)
    {
        $this->message = (string) $message;

        $this->updateBody();

        return $this;
    }

    /**
     * Get the "more info"
     *
     * @access public
     * @return mixed
     */
    public function getMoreInfo()
    {
        return $this->more_info;
    }

    /**
     * Set the "more info"
     *
     * @param array|object $more_info
     * @access public
     * @return ApiResponse
     */
    public function setMoreInfo($more_info)
    {
        $this->more_info = (object) $more_info;

        $this->updateBody();

        return $this;
    }

    /**
     * Set the raw data
     *
     * @param mixed $data
     * @access protected
     * @return ApiResponse
     */
    protected function setRawData($data)
    {
        return parent::setData($data);
    }

    /**
     * Get the raw data
     *
     * @access protected
     * @return mixed
     */
    protected function getRawData()
    {
        return parent::getData();
    }

    /**
     * Get the response data in a formatted structure
     *
     * @access protected
     * @return ArrayObject
     */
    protected function getFormattedResponseData()
    {
        // Create our formatted structure
        $formatted = (object) [];

        // Create our meta-structure
        $formatted->meta = (object) [
            'status_code' => (int) $this->code(),
            'status' => (string) $this->getStatusSlug(),
            'message' => (string) $this->getMessage(),
            'more_info' => $this->getMoreInfo() ?: (object) [],
        ];

        // Assign our data
        $formatted->data = $this->getData() ?: (object) [];

        return $formatted;
    }

    /**
     * Update our response's body
     *
     * Set's the response's body to a JSON
     * encoded version of the formatted
     * response data
     *
     * @access protected
     * @return ApiResponse
     */
    protected function updateBody()
    {
        $this->setRawData(
            $this->getFormattedResponseData()
        );

        return $this;
    }
}
