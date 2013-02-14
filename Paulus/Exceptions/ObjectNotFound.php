<?php

namespace Paulus\Exceptions;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\Paulus\Exceptions\Traits\ApiExceptionBase,
	\OutOfBoundsException;

/**
 * ObjectNotFound 
 *
 * ObjectNotFound Exception 
 * 
 * @uses OutOfBoundsException
 * @uses ApiException
 * @package		Paulus\Exceptions
 */
class ObjectNotFound extends OutOfBoundsException implements ApiException {
	// Use trait
	use ApiExceptionBase;

	// Define unique properties
	protected $code = 404;
	protected $slug = 'NOT_FOUND';
	protected $message = 'Object does not exist';

} // End class ObjectNotFound
