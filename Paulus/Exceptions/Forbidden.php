<?php

namespace Paulus\Exceptions;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\OutOfBoundsException;

// Forbidden Exception 
class Forbidden extends OutOfBoundsException implements ApiException {

	// Define unique properties
	protected $code = 403;
	protected $slug = 'FORBIDDEN';
	protected $message = 'You are forbidden to access or modify this';

	// Return the slug
	public function getSlug() {
		return $this->slug;
	}

	// Alias of getSlug
	public function get_slug() {
		return $this->getSlug();
	}

} // End class Forbidden
