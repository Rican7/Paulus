<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.3
 */

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
