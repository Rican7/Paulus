<?php

namespace Paulus\Exceptions;

use	\Paulus\ApiException,
	\Paulus\ApiVerboseException,
	\OutOfBoundsException;

// WrongMethod Exception 
class WrongMethod extends OutOfBoundsException implements ApiVerboseException {

	// Define unique properties
	protected $code = 405;
	protected $slug = 'METHOD_NOT_ALLOWED';
	protected $more_info = array();

	// Return the slug
	public function getSlug() {
		return $this->slug;
	}

	// Return the more_info
	public function getMoreInfo() {
		return $this->more_info;
	}

	// Set the more_info
	public function setMoreInfo( array $more_info ) {
		return $this->more_info = $more_info;
	}

	// Alias of getSlug
	public function get_slug() {
		return $this->getSlug();
	}

	// Alias of getMoreInfo
	public function get_more_info() {
		return $this->getMoreInfo();
	}

	// Alias of setMoreInfo
	public function set_more_info( array $more_info ) {
		return $this->setMoreInfo( $more_info );
	}

} // End class WrongMethod
