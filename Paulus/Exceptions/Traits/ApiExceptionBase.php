<?php

namespace Paulus\Exceptions\Traits;

/**
 * ApiExceptionBase 
 *
 * ApiExceptionBase Exception Trait
 * 
 * @package		Paulus\Exceptions\Traits
 */
trait ApiExceptionBase {

	/**
	 * getSlug
	 *
	 * Returns the exception's "slug" property/attribute
	 * 
	 * @access public
	 * @return string|null
	 */
	public function getSlug() {
		return $this->slug;
	} // End function getSlug

	/**
	 * get_slug
	 *
	 * Alias of getSlug
	 * 
	 * @see getSlug()		Documentation for self::getSlug()
	 * @access public
	 * @return string|null
	 */
	public function get_slug() {
		return $this->getSlug();
	} // End function get_slug

} // End trait ApiExceptionBase
