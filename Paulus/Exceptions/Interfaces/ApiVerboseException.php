<?php

namespace Paulus\Exceptions\Interfaces;

/**
 * ApiVerboseException 
 *
 * ApiVerboseException Exception Interface
 * 
 * @uses ApiException
 * @package		Paulus\Exceptions\Interfaces
 */
interface ApiVerboseException extends ApiException {

	/**
	 * getMoreInfo
	 *
	 * Force implementation of a "more_info" property
	 * 
	 * @access public
	 * @return array
	 */
	public function getMoreInfo();

	/**
	 * setMoreInfo
	 *
	 * Force implementation of a "more_info" property
	 * 
	 * @param array $more_info 
	 * @access public
	 * @return array
	 */
	public function setMoreInfo( array $more_info );

	/**
	 * get_more_info
	 *
	 * Also force an alias for code-style
	 * 
	 * @access public
	 * @return array
	 */
	public function get_more_info();

	/**
	 * set_more_info
	 *
	 * Also force an alias for code-style
	 * 
	 * @param array $more_info 
	 * @access public
	 * @return array
	 */
	public function set_more_info( array $more_info );

} // End interface ApiVerboseException
