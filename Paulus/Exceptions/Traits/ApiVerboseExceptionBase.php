<?php

namespace Paulus\Exceptions\Traits;

// ApiVerboseExceptionBase Exception Trait
trait ApiVerboseExceptionBase {
	// Use trait
	use ApiExceptionBase;

	// Return the more_info
	public function getMoreInfo() {
		return $this->more_info;
	}

	// Set the more_info
	public function setMoreInfo( array $more_info ) {
		return $this->more_info = $more_info;
	}

	// Alias of getMoreInfo
	public function get_more_info() {
		return $this->getMoreInfo();
	}

	// Alias of setMoreInfo
	public function set_more_info( array $more_info ) {
		return $this->setMoreInfo( $more_info );
	}

} // End trait ApiVerboseExceptionBase
