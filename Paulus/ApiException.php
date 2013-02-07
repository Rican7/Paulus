<?php

namespace Paulus;

// ApiException Exception Interface
interface ApiException {

	// Force implementation of a "slug" property
	public function getSlug();

	// Also force an alias for code-style
	public function get_slug();

} // End interface ApiException
