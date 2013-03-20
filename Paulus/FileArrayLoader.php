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

namespace Paulus;

use \RuntimeException;

/**
 * FileArrayLoader 
 *
 * Class designed to load files from a specified directory
 * and load a callback's result into an array
 * 
 * @uses		RuntimeException
 * @package		Paulus
 */
class FileArrayLoader {

	/*
	 * Declare properties
	 */
	protected $directory;
	protected $ignore_prefix;
	protected $callback_name;
	
	/**
	 * __construct
	 * 
	 * @param string $directory		A directory (or file) name to load our file list from
	 * @param string $ignore_prefix A prefix that our file list loader will ignore
	 * @param string $callback_name The name of our callback function to load the array
	 * @access public
	 * @return void
	 */
	public function __construct( $directory, $ignore_prefix = '_', $callback_name = 'load' ) {
		// Set our properties
		$this->directory = $directory;
		$this->ignore_prefix = $ignore_prefix;
		$this->callback_name = $callback_name;
	}

	/**
	 * load
	 *
	 * Execute our loading and array logic
	 * 
	 * @access public
	 * @return void
	 */
	public function load() {
		// Create our array to return
		$return_array = array();

		// Did we pass a directory or file
		if ( is_readable( $this->directory ) ) {
			// Is our passed directory really a file?
			if ( is_file( $this->directory ) ) {
				// Add our returned array to our "return_array"
				$return_array = $return_array + $this->load_array_from_file( $this->directory );
			}
			// Must be a directory
			else {
				// Load our file list
				$file_list = $this->load_file_list( $this->directory, $this->ignore_prefix );

				// Loop through each file in our list
				foreach( $file_list as $file ) {
					// Add our returned array to our "return_array"
					$return_array = $return_array + $this->load_array_from_file( $file, $this->directory );
				}
			}
		}
		else {
			// Throw an exception
			throw new RuntimeException( 'Unable to read file/directory ' . $this->directory );
		}

		return $return_array;
	}

	/**
	 * load_file_list
	 *
	 * Function to scan our file directory and load the file list
	 * 
	 * @param string $directory		A directory name to load our file list from
	 * @param string $ignore_prefix A prefix that our file list loader will ignore
	 * @access protected
	 * @return void
	 */
	protected function load_file_list( $directory, $ignore_prefix ) {
		// Get an array of all of the files in the config directory
		$found_files = scandir( $directory );

		// Create an array to return
		$valid_files = array();

		foreach( $found_files as $file ) {
			// Is it a valid file?
			if ( is_file( $directory . $file ) && ( strpos( $file, $ignore_prefix ) !== 0 ) ) {
				$valid_files[] = $file;
			}
		}

		return $valid_files;
	}

	/**
	 * load_array_from_file
	 *
	 * Function to load our file's callback result into an array
	 * 
	 * @access protected
	 * @return void
	 */
	protected function load_array_from_file( $file, $base_directory = null ) {
		// Grab our file's basename
		$file_base = basename( $file, '.php' );

		// Define our file path
		$file_path = $base_directory ? $base_directory . $file : $file;

		// Include the file
		require( $file_path );

		// Set the file's returned configuration in a namespaced key
		$array = array( $file_base => ${$this->callback_name}() );

		// Garbage collect
		unset( ${$this->callback_name} );

		return $array;
	}

} // End class FileArrayLoader
