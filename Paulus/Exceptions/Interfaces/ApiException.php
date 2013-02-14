<?php

namespace Paulus\Exceptions\Interfaces;

/**
 * ApiException 
 *
 * ApiException Exception Interface
 * 
 * @package		Paulus\Exceptions\Interfaces
 */
interface ApiException {

	/**
	 * getSlug
	 *
	 * Force implementation of a "slug" property
	 * 
	 * @access public
	 * @return string|mixed
	 */
	public function getSlug();

	/**
	 * get_slug
	 *
	 * Also force an alias for code-style
	 * 
	 * @access public
	 * @return string|mixed
	 */
	public function get_slug();

} // End interface ApiException
