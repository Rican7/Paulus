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

/**
 * AbstractFileLoader
 *
 * An abstract class making it easier to define
 * custom file and directory loaders
 *
 * @uses    IteratorAggregate
 * @abstract
 * @package Paulus
 */
abstract class AbstractFileLoader implements IteratorAggregate
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
     * @access public
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->ignore_prefix = static::DEFAULT_IGNORE_PREFIX;
    }

    /**
     * Set the prefix of file names to ignore
     *
     * @param mixed $ignore_prefix
     * @access public
     * @return void
     */
    public function setIgnorePrefix($ignore_prefix)
    {
        $this->ignore_prefix = $ignore_prefix;
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
     * Quick check to see if a file extension
     * is valid extension or not for processing
     *
     * @param string $extension
     * @access protected
     * @return boolean
     */
    protected function isValidFileExtension($extension)
    {
        return ($extension === static::VALID_FILE_EXTENSION);
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
            && $this->isValidFileExtension($file->getExtension())
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
     * Process the file being loaded
     *
     * @param SplFileInfo $file     The file to process
     * @param mixed $load_return    A reference to the value to be returned by `load()`
     * @abstract
     * @access protected
     * @return void
     */
    abstract protected function processFile(SplFileInfo $file, &$load_return);

    /**
     * Load the files found in the directory
     * and return an array with their data
     *
     * @access public
     * @throws UnexpectedValueException If the value returned from the file isn't callable
     * @return mixed
     */
    public function load()
    {
        $return_val = [];

        // Iterate over ourselves (haha, awesome)
        foreach ($this as $file) {
            // Make sure our file is valid
            if (!$this->isValid($file)) {
                continue;
            }

            // Process our file
            $this->processFile($file, $return_val);
        }

        return $return_val;
    }
}
