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

namespace Paulus\Request;

use Klein\Request as KleinRequest;

/**
 * Request
 *
 * @uses    KleinRequest
 * @package Paulus\Request
 */
class Request extends KleinRequest
{

    /**
     * Constants
     */

    /**
     * The regular expression used to
     * split the authorization header
     * into its respective parts
     *
     * @link http://oauth.googlecode.com/svn/code/php/OAuth.php OAuthUtil::split_header()
     * @const string
     */
    const AUTH_HEADER_SPLIT_REGEX = '/([a-z_-]*)=(:?"([^"]*)"|([^,]*))/';


    /**
     * Methods
     */

    /**
     * Read our authorization header and return its parts
     *
     * Returns an object that contains 3 properties:
     * type => The type of the authorization
     * string => The string of the authorization that follows the type
     * params => The split params of the authorization header
     *
     * @access protected
     * @return object|boolean
     */
    public function readAuthHeader()
    {
        $auth_header = $this->headers->get('AUTHORIZATION');

        if (null !== $auth_header) {
            // Grab our type
            $auth_header_type = explode(' ', $auth_header)[0];

            // Grab our string, and do some string processing
            $auth_header_string = str_replace(
                "\r\n",
                '',
                trim(substr($auth_header, strlen($auth_header_type)))
            );

            // Process our string into params
            $auth_header_params = array();

            if (preg_match_all(static::AUTH_HEADER_SPLIT_REGEX, $auth_header_string, $params)) {
                foreach ($params[1] as $key => $val) {
                    $auth_header_params[$val] = trim($params[2][$key], '"');
                }
            }

            // Combine our parts
            $auth_header_parts = (object) array(
                'type' => $auth_header_type,
                'string' => $auth_header_string,
                'params' => $auth_header_params,
            );

            return $auth_header_parts;
        }

        return false;
    }
}
