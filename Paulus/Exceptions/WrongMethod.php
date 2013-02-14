<?php

namespace Paulus\Exceptions;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\Paulus\Exceptions\Interfaces\ApiVerboseException,
	\Paulus\Exceptions\Traits\ApiVerboseExceptionBase,
	\OutOfBoundsException;

/**
 * WrongMethod 
 *
 * WrongMethod Exception 
 * 
 * @uses OutOfBoundsException
 * @uses ApiVerboseException
 * @package		Paulus\Exceptions
 */
class WrongMethod extends OutOfBoundsException implements ApiVerboseException {
	// Use trait
	use ApiVerboseExceptionBase;

	// Define unique properties
	protected $code = 405;
	protected $slug = 'METHOD_NOT_ALLOWED';
	protected $message = 'The wrong method was called on this endpoint';
	protected $more_info = array();

} // End class WrongMethod
