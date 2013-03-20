<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.4
 */

namespace Paulus;

/**
 * Util 
 *
 * Static utility class for useful generic functions
 * 
 * @package		Paulus
 */
class Util {

	/**
	 * object_to_array
	 * 
	 * Quickly create a function to convert the object to an array
	 * Source: http://goo.gl/uTLGf
	 *
	 * @param object $d		The object to convert to an array
	 * @static
	 * @access public
	 * @return void
	 */
	public static function object_to_array($d) {
		if ( is_object( $d ) ) {
			// Gets the properties of the given object with get_object_vars function
			$d = get_object_vars( $d );
		}

		if ( is_array( $d ) ) {
			/*
			 * Return array converted to object
			 * Using __FUNCTION__ (Magic constant)
			 * for recursive call
			 */
			return array_map( array( __CLASS__, __FUNCTION__ ), $d);
		}
		else {
			// Return array
			return $d;
		}
	}

	/**
	 * array_merge_recursive_distinct
	 *
	 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
	 * keys to arrays rather than overwriting the value in the first array with the duplicate
	 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
	 * this happens (documented behavior):
	 *
	 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
	 *     => array('key' => array('org value', 'new value'));
	 *
	 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
	 * Matching keys' values in the second array overwrite those in the first array, as is the
	 * case with array_merge, i.e.:
	 *
	 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
	 *     => array('key' => array('new value'));
	 *
	 * Parameters are passed by reference, though only for performance reasons. They're not
	 * altered by this function.
	 *
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
	 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
	 */
	public static function array_merge_recursive_distinct( array &$array1, array &$array2 ) {
		$merged = $array1;

		foreach ( $array2 as $key => &$value ) {
			if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ) {
				$merged [$key] = self::array_merge_recursive_distinct( $merged [$key], $value );
			}
			else {
				$merged [$key] = $value;
			}
		}

		return $merged;
	}

} // End class Util
