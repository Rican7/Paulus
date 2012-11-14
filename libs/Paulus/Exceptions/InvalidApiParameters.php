<?php

namespace Paulus\Exceptions;

use	\Paulus\ApiException,
	\InvalidArgumentException;

// InvalidApiParameters Exception 
class InvalidApiParameters extends InvalidArgumentException implements ApiException {

	// Define unique properties
	protected $code = 400;
	protected $slug = 'INVALID_API_PARAMETERS';

	// Return the slug
	public function getSlug() {
		return $this->slug;
	}

	// Alias of getSlug
	public function get_slug() {
		return $this->getSlug();
	}

} // End class InvalidApiParameters
