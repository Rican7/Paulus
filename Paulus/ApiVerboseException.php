<?php

namespace Paulus;

use	\Paulus\ApiException;

// ApiVerboseException Exception Interface
interface ApiVerboseException extends ApiException {

	// Force implementation of a "more_info" property
	public function getMoreInfo();
	public function setMoreInfo( array $more_info );

	// Also force an alias for code-style
	public function get_more_info();
	public function set_more_info( array $more_info );

} // End interface ApiVerboseException
