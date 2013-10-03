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

use Klein\AbstractResponse;

/**
 * JsonResponse
 *
 * @uses    Klein\AbstractResponse
 * @package Paulus\Response
 */
class JsonResponse extends AbstractResponse
{

    /**
     * Properties
     */

    /**
     * The raw unencoded data
     *
     * @var mixed
     * @access protected
     */
    protected $raw_data;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param mixed $data           The response data
     * @param int $status_code      The status code
     * @param array $headers        The response header "hash"
     * @access public
     */
    public function __construct($data = null, $status_code = null, array $headers = [])
    {
        parent::__construct('', $status_code, $headers);

        // Set our data and lock our response
        $this->setData($data);
        $this->lock();
    }

    /**
     * Set the response's data
     *
     * @param mixed $data   The data
     * @access public
     * @return JsonResponse
     */
    public function setData($data = null)
    {
        // Require that the response be unlocked before changing it
        $this->requireUnlocked();

        if (null === $data) {
            $data = (object) [];
        }

        // Keep our raw data
        $this->raw_data = $data;

        // Set our body too
        $this->body(static::encode($data));

        // Update our content header
        $this->updateContentHeader();

        return $this;
    }

    /**
     * Get the response's data
     *
     * @access public
     * @return mixed
     */
    public function getData()
    {
        return $this->raw_data;
    }

    /**
     * Update the content headers to make sure
     * that they accurately reflect the data type
     *
     * @access protected
     * @return JsonResponse
     */
    protected function updateContentHeader()
    {
        $header_key = 'Content-Type';

        // Don't overwrite a custom header
        if (!$this->headers()->exists($header_key)) {
            $this->headers()->set($header_key, 'application/json');
        }

        return $this;
    }

    /**
     * Encode and return the response data
     *
     * @access public
     * @return string
     */
    public static function encode($data)
    {
        /**
         * Set our default encoding flags
         * http://www.php.net/manual/en/json.constants.php
         */
        $encode_flags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

        return json_encode($data, $options);
    }
}
