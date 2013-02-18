<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.2
 */

namespace Paulus\Exceptions;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\Paulus\Exceptions\Traits\ApiExceptionBase,
	\OutOfBoundsException;

/**
 * EndpointNotFound 
 *
 * EndpointNotFound Exception 
 * 
 * @uses OutOfBoundsException
 * @uses ApiException
 * @package		Paulus\Exceptions
 */
class EndpointNotFound extends OutOfBoundsException implements ApiException {
	// Use trait
	use ApiExceptionBase;

	// Define unique properties
	protected $code = 404;
	protected $slug = 'NOT_FOUND';
	protected $message = 'Unable to find the endpoint you requested';

} // End class EndpointNotFound
