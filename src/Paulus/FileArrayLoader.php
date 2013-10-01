<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful services
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2013 Trevor Suarez
 * @link        https://github.com/Rican7/Paulus
 * @license     https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version     2.0.0
 */

namespace Paulus;

use FilesystemIterator;
use IteratorAggregate;
use SplFileInfo;
use UnexpectedValueException;

/**
 * FileArrayLoader
 *
 * @uses    IteratorAggregate
 * @package Paulus
 */
class FileArrayLoader implements IteratorAggregate
{

    /**
     * Constants
     */

    /**
     * The default prefix to look for
     * when ignoring files
     *
     * @const string
     */
    const DEFAULT_IGNORE_PREFIX = '_';

    /**
     * The file extension deemed valid
     * for requiring as executable
     *
     * @const string
     */
    const VALID_FILE_EXTENSION = 'php';


    /**
     * Properties
     */

    /**
     * The path of the directory to traverse
     *
     * @var string
     * @access protected
     */
    protected $path;

    /**
     * The prefix to look for when
     * ignoring files to load
     *
     * @var string
     * @access protected
     */
    protected $ignore_prefix;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param string $path          The path to traverse
     * @param string $ignore_prefix The prefix of file names to ignore
     * @access public
     */
    public function __construct($path, $ignore_prefix = null)
    {
        $this->path = $path;
        $this->ignore_prefix = (null !== $ignore_prefix) ? $ignore_prefix : static::DEFAULT_IGNORE_PREFIX;
    }

    /**
     * Quick check to see if a filename
     * should be ignored based on the prefix
     *
     * @param string $filename
     * @access protected
     * @return boolean
     */
    protected function isIgnoredFilename($filename)
    {
        if (!empty($this->ignore_prefix)) {
            return (strpos($filename, $this->ignore_prefix) === 0);
        }

        return false;
    }

    /**
     * Check if the file is valid or not
     * based on a few rules
     *
     * @param SplFileInfo $file
     * @access protected
     * @return boolean
     */
    protected function isValid(SplFileInfo $file)
    {
        /**
         * Make sure that our file meets the following conditions:
         *  - its name doesn't start with our ignored prefix
         *  - its a valid executable/includable file (a php file)
         *  - its readable
         */
        if ($this->isIgnoredFilename($file->getFilename()) !== true
            && $file->getExtension() === static::VALID_FILE_EXTENSION
            && $file->isReadable()) {

            return true;
        }

        return false;
    }

    /**
     * Get a FilesystemIterator instance
     *
     * @access public
     * @return FilesystemIterator
     */
    public function getIterator()
    {
        // Create our bitwise flags
        $flags = FilesystemIterator::KEY_AS_PATHNAME
            | FilesystemIterator::CURRENT_AS_FILEINFO
            | FilesystemIterator::SKIP_DOTS;

        return new FilesystemIterator($this->path, $flags);
    }

    /**
     * Load the files found in the directory
     * and return an array with their data
     *
     * @access public
     * @throws UnexpectedValueException If the value returned from the file isn't callable
     * @return array
     */
    public function load()
    {
        $return_array = [];

        // Iterate over ourselves (haha, awesome)
        foreach ($this as $file) {
            // Make sure our file is valid
            if (!$this->isValid($file)) {
                continue;
            }

            // Include the file
            $callback = require($file);

            // Verify that we got back a callable callback
            if (!is_callable($callback)) {
                throw new UnexpectedValueException(
                    sprintf('Returned value from included file "%s" is not callable', $file->getPathname())
                );
            }

            // Get our name for our array key
            $key_name = $file->getBasename('.'. static::VALID_FILE_EXTENSION);

            $return_array[$key_name] = $callback();
        }

        return $return_array;
    }
}
