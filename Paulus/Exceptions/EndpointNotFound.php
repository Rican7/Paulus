<?php

namespace Paulus\Exceptions;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\Paulus\Exceptions\Traits\ApiExceptionBase,
	\OutOfBoundsException;

// EndpointNotFound Exception 
class EndpointNotFound extends OutOfBoundsException implements ApiException {
	// Use trait
	use ApiExceptionBase;

	// Define unique properties
	protected $code = 404;
	protected $slug = 'NOT_FOUND';
	protected $message = 'Unable to find the endpoint you requested';

} // End class EndpointNotFound
