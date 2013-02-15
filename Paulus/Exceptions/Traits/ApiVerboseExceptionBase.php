<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.0
 */

namespace Paulus\Exceptions\Traits;

/**
 * ApiVerboseExceptionBase
 *
 * ApiVerboseExceptionBase Exception Trait
 * 
 * @package		Paulus\Exceptions\Traits
 * @see			ApiExceptionBase
 */
trait ApiVerboseExceptionBase {
	// Use trait
	use ApiExceptionBase;

	/**
	 * getMoreInfo
	 *
	 * Returns the exception's "more_info" property/attribute
	 * 
	 * @access public
	 * @return array
	 */
	public function getMoreInfo() {
		return $this->more_info;
	} // End function getMoreInfo

	/**
	 * setMoreInfo
	 * 
	 * Sets the exception's "more_info" property/attribute
	 *
	 * @param array $more_info 
	 * @access public
	 * @return array
	 */
	public function setMoreInfo( array $more_info ) {
		return $this->more_info = $more_info;
	} // End function setMoreInfo

	/**
	 * get_more_info
	 *
	 * Alias of getMoreInfo
	 * 
	 * @see getMoreInfo()	Documentation for self::getMoreInfo()
	 * @access public
	 * @return array
	 */
	public function get_more_info() {
		return $this->getMoreInfo();
	} // End function get_more_info

	/**
	 * set_more_info
	 *
	 * Alias of setMoreInfo
	 * 
	 * @see setMoreInfo()	Documentation for self::setMoreInfo()
	 * @param array $more_info 
	 * @access public
	 * @return array
	 */
	public function set_more_info( array $more_info ) {
		return $this->setMoreInfo( $more_info );
	} // End function set_more_info

} // End trait ApiVerboseExceptionBase
