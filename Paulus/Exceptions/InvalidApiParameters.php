<?php

namespace Paulus\Exceptions;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\Paulus\Exceptions\Traits\ApiExceptionBase,
	\InvalidArgumentException;

// InvalidApiParameters Exception 
class InvalidApiParameters extends InvalidArgumentException implements ApiException {
	// Use trait
	use ApiExceptionBase;

	// Define unique properties
	protected $code = 400;
	protected $slug = 'INVALID_API_PARAMETERS';
	protected $message = 'The posted data did not pass validation';

} // End class InvalidApiParameters
