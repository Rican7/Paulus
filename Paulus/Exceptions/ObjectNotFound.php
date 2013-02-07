<?php

namespace Paulus\Exceptions;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\OutOfBoundsException;

// ObjectNotFound Exception 
class ObjectNotFound extends OutOfBoundsException implements ApiException {

	// Define unique properties
	protected $code = 404;
	protected $slug = 'NOT_FOUND';
	protected $message = 'Object does not exist';

	// Return the slug
	public function getSlug() {
		return $this->slug;
	}

	// Alias of getSlug
	public function get_slug() {
		return $this->getSlug();
	}

} // End class ObjectNotFound
