<?php

namespace Paulus\Exceptions;

use	\Paulus\ApiException,
	\OutOfBoundsException;

// EndpointNotFound Exception 
class EndpointNotFound extends OutOfBoundsException implements ApiException {

	// Define unique properties
	protected $code = 404;
	protected $slug = 'NOT_FOUND';
	protected $message = 'Unable to find the endpoint you requested';

	// Return the slug
	public function getSlug() {
		return $this->slug;
	}

	// Alias of getSlug
	public function get_slug() {
		return $this->getSlug();
	}

} // End class EndpointNotFound
