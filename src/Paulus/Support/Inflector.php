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

namespace Paulus\Support;

/**
 * Inflector
 *
 * For transforming and converting strings into
 * different forms, usually involving words
 *
 * @package Paulus\Support
 */
class Inflector
{

    /**
     * Convert a string to one that follows the
     * PSR-1 class constant style rule
     *
     * Example:
     * input:   'Not found'
     * output:  'NOT_FOUND'
     *
     * @link http://git.io/7QpENw
     * @param mixed $word
     * @static
     * @access public
     * @return void
     */
    public static function constantStringify($string)
    {
        // Upper case all the things
        $string = strtoupper($string);

        // Convert all spaces to underscores
        $string = str_replace(' ', '_', $string);

        // Now remove all weird characters
        $string = preg_replace('/[^A-Z0-9_-]/', '', $string);

        return $string;
    }

    /**
     * Convert a string from a namespace-like
     * representation to a directory path
     *
     * Example:
     * input:   'Paulus\Support\Inflector'
     * output:  'Paulus/Support/Inflector'
     *
     * @param string $namespace
     * @static
     * @access public
     * @return string
     */
    public static function namespaceToPath($namespace = __NAMESPACE__)
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
    }

    /**
     * Convert a string from a directory path
     * representation to a namespace
     *
     * Example:
     * input:   'Paulus/Support/Inflector'
     * output:  'Paulus\Support\Inflector'
     *
     * @param string $namespace
     * @static
     * @access public
     * @return string
     */
    public static function pathToNamespace($path)
    {
        return str_replace(DIRECTORY_SEPARATOR, '\\', $path);
    }

    /**
     * Convert a URL namespace to a PHP PSR-0
     * compatible camel cased namespace
     *
     * Example:
     * input:   '/php-fig/fig-standards/blob'
     * output:  '\PhpFig\FigStandards\Blob'
     *
     * @param string $url_namespace
     * @static
     * @access public
     * @return string
     */
    public static function urlNamespaceToClassNamespace($url_namespace)
    {
        // Get an array separating each part of the URL
        $parts = explode('/', $url_namespace);

        // Apply a few rules to each part
        $parts = array_map(
            function ($string) {
                $string = str_replace('-', '_', $string); // Turn all hyphens to underscores
                $string = str_replace('_', ' ', $string); // Turn all underscores to spaces (for easy casing)
                $string = ucwords($string); // Uppercase each first letter of each "word"
                $string = str_replace(' ', '', $string); // Remove spaces. BOOM! PSR-compatible camel casing

                return $string;
            },
            $parts
        );

        // Put the parts back together and return
        return implode('\\', $parts);
    }
}
