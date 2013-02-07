<?php

namespace Paulus\Exceptions;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\Paulus\Exceptions\Traits\ApiExceptionBase,
	\OutOfBoundsException;

// Forbidden Exception 
class Forbidden extends OutOfBoundsException implements ApiException {
	// Use trait
	use ApiExceptionBase;

	// Define unique properties
	protected $code = 403;
	protected $slug = 'FORBIDDEN';
	protected $message = 'You are forbidden to access or modify this';

} // End class Forbidden
