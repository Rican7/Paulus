<?php

namespace Paulus\Exceptions\Traits;

// ApiExceptionBase Exception Trait
trait ApiExceptionBase {

	// Return the slug
	public function getSlug() {
		return $this->slug;
	}

	// Alias of getSlug
	public function get_slug() {
		return $this->getSlug();
	}

} // End trait ApiExceptionBase
