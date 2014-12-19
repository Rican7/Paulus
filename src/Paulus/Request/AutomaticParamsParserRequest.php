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

use Paulus\Exception\Http\InvalidRequestSyntax;

/**
 * AutomaticParamsParserRequest
 *
 * @uses    Request
 * @package Paulus\Request
 */
class AutomaticParamsParserRequest extends Request
{

    /**
     * Create a new request from the PHP request globals
     *
     * @see Klein\Request::createFromGlobals()
     * @static
     * @access public
     * @return AutomaticParamsParserRequest
     */
    public static function createFromGlobals()
    {
        // First, get our object from our parent handler
        $request = parent::createFromGlobals();

        // Should we even be handling params?
        if ($request->method('POST')
            || $request->method('PUT')
            || $request->method('PATCH')
            || $request->method('DELETE')) {

            // Parse Form URL Encoded attributes
            if (stripos($request->headers()->get('content-type'), 'application/x-www-form-urlencoded') !== false
                && !$request->method('POST')) {

                // Parse the body into a params array
                parse_str($request->body(), $params);

                // Replace the post params with the parse params
                $request->paramsPost()->replace($params);

            } elseif (stripos($request->headers()->get('content-type'), 'application/json') !== false
                || stripos($request->headers()->get('content-type'), 'application/x-json') !== false) {

                // Decode the JSON body
                $params = json_decode($request->body(), true);

                // If there were no decoding errors
                if (JSON_ERROR_NONE === json_last_error()) {
                    // Replace the post params with the parse params
                    $request->paramsPost()->replace((array) $params);

                } else {
                    // Must have been a decoding error
                    throw new InvalidRequestSyntax();
                }
            }
        }

        return $request;
    }
}
