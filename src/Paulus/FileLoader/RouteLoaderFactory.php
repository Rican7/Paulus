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

namespace Paulus\FileLoader;

use FilesystemIterator;
use Paulus\Exception\UnableToInferRouteDirectoryException;
use Paulus\Router;
use SplFileInfo;

/**
 * RouteLoaderFactory
 *
 * @package Paulus\FileLoader;
 */
class RouteLoaderFactory
{

    /**
     * Constants
     */

    /**
     * The name of the route directory
     * name to search for when inferring
     * the route directory path
     *
     * @const string
     */
    const ROUTE_DIR_NAME = 'routes';

    /**
     * The maximum number of times that
     * the scan loop will attempt to traverse
     * up the directory tree
     *
     * @const int
     */
    const NUMBER_OF_ITERATIONS = 8;


    /**
     * Methods
     */

    /**
     * Convert a namespace to a directory path
     *
     * @param string $namespace
     * @static
     * @access protected
     * @return string
     */
    protected static function namespaceToPath($namespace = __NAMESPACE__)
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
    }

    /**
     * Strip a section from the end of a path
     *
     * @param string $path
     * @param string $strip
     * @static
     * @access protected
     * @return string
     */
    protected static function stripFromPath($path, $strip)
    {
        // Grab the position starting from the end of the string
        $position = strrpos($path, $strip);

        if ($position !== false) {
            $path = substr($path, 0, $position);
        }

        return $path;
    }

    /**
     * Scan a directory path for a particular named file/directory
     *
     * @param string $path      The path to scan
     * @param string $find_name The name of the file/directory to find
     * @param boolean $is_dir   Whether or not to search for a directory (instead of a file)
     * @static
     * @access protected
     * @return boolean|SplFileInfo
     */
    protected static function scanDirectoryFor($path, $find_name, $is_dir = true)
    {
        // Define our iterator's flags
        $iterator_flags = FilesystemIterator::KEY_AS_PATHNAME
            | FilesystemIterator::CURRENT_AS_FILEINFO
            | FilesystemIterator::SKIP_DOTS;

        $iterator = new FilesystemIterator($path, $iterator_flags);

        foreach ($iterator as $file) {
            // Did we match our search?
            if ($file->getFilename() === $find_name) {
                // Are we looking for a directory?
                if ($is_dir && !$file->isDir()) {
                    continue;
                }

                return $file;
            }
        }

        return false;
    }

    /**
     * Infer the route directory by attempting to traverse
     * up the directory tree until it finds a route directory
     *
     * ... Its Magic!
     *
     * Returns a SplFileInfo object if found, or false if it couldn't
     * find the directory
     *
     * @param string $start_directory
     * @static
     * @access protected
     * @return boolean|SplFileInfo
     */
    protected static function inferRouteDirectory($start_directory = __DIR__)
    {
        // First, let's strip our directory namespace
        $dir = static::stripFromPath($start_directory, static::namespaceToPath());

        // Setup our loop variables
        $found = false; // Have we found it?
        $tries = 0;     // Keep track of the times we've tried, so we don't loop forever

        while (!$found && $tries < static::NUMBER_OF_ITERATIONS) {
            $returned = static::scanDirectoryFor($dir, static::ROUTE_DIR_NAME, true);

            // If we got a SplFileInfo instance back, we must have found it
            $found = ($returned instanceof SplFileInfo);

            if (!$found) {
                // Try going up a directory for the next search
                $dir = $dir .'/..';
            }

            $tries++;
        }

        return $returned;
    }

    /**
     * Build the RouteLoader by inferring the route directory
     *
     * @param string $start_directory
     * @throws UnableToInferRouteDirectoryException If we failed to infer the directory
     * @static
     * @access public
     * @return RouteLoader
     */
    public static function buildByDirectoryInferring(Router $router, $start_directory = __DIR__)
    {
        $path = static::inferRouteDirectory($start_directory);

        if ($path === false) {
            // Let them know that we couldn't find it
            throw new UnableToInferRouteDirectoryException();
        }

        return new RouteLoader($path, $router);
    }
}
