<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.10.0
 */

namespace Paulus\Exceptions;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\Paulus\Exceptions\Traits\ApiExceptionBase,
	\OutOfBoundsException;

/**
 * Forbidden 
 *
 * Forbidden Exception 
 * 
 * @uses OutOfBoundsException
 * @uses ApiException
 * @package		Paulus\Exceptions
 */
class Forbidden extends OutOfBoundsException implements ApiException {
	// Use trait
	use ApiExceptionBase;

	// Define unique properties
	protected $code = 403;
	protected $slug = 'FORBIDDEN';
	protected $message = 'You are forbidden to access or modify this';

} // End class Forbidden
