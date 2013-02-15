<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.0
 */

namespace Paulus\Exceptions;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\Paulus\Exceptions\Traits\ApiExceptionBase,
	\InvalidArgumentException;

/**
 * Unauthorized 
 *
 * Unauthorized Exception 
 * 
 * @uses InvalidArgumentException
 * @uses ApiException
 * @package		Paulus\Exceptions
 */
class Unauthorized extends InvalidArgumentException implements ApiException {
	// Use trait
	use ApiExceptionBase;

	// Define unique properties
	protected $code = 401;
	protected $slug = 'UNAUTHORIZED';
	protected $message = 'You are not authorized to access or modify this';

} // End class Unauthorized
