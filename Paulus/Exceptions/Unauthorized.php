<?php

namespace Paulus\Exceptions;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\InvalidArgumentException;

// Unauthorized Exception 
class Unauthorized extends InvalidArgumentException implements ApiException {

	// Define unique properties
	protected $code = 401;
	protected $slug = 'UNAUTHORIZED';
	protected $message = 'You are not authorized to access or modify this';

	// Return the slug
	public function getSlug() {
		return $this->slug;
	}

	// Alias of getSlug
	public function get_slug() {
		return $this->getSlug();
	}

} // End class Unauthorized
