<?php

namespace Paulus\Exceptions;

use	\Paulus\ApiException,
	\OutOfBoundsException;

// Forbidden Exception 
class Forbidden extends OutOfBoundsException implements ApiException {

	// Define unique properties
	protected $code = 403;
	protected $slug = 'FORBIDDEN';

	// Return the slug
	public function getSlug() {
		return $this->slug;
	}

	// Alias of getSlug
	public function get_slug() {
		return $this->getSlug();
	}

} // End class Forbidden
