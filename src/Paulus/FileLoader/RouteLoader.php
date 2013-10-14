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

use Paulus\Exception\RouteAutoLoadFailureException;
use Paulus\Router;
use SplFileInfo;

/**
 * RouteLoader
 *
 * The class responsible for iterating through a
 * directory, loading route files, and namespacing
 * them according to their file name
 *
 * Also, this class can auto initialize a controller
 * for that namespace, so a shared instance can be used
 * across all matches under the same URL namespace
 *
 * @uses    AbstractFileLoader
 * @package Paulus\FileLoader
 */
class RouteLoader extends AbstractFileLoader
{

    /**
     * Properties
     */

    /**
     * The Router instance to use when
     * defining the loaded routes
     *
     * @var Router
     * @access protected
     */
    protected $router;

    /**
     * The list of valid routes to filter
     * the loading by
     *
     * @var array
     * @access protected
     */
    protected $valid_routes;

    /**
     * The name of the top-level route
     *
     * @var string
     * @access protected
     */
    protected $top_level_route = 'index';

    /**
     * Whether or not it should be attempted
     * to automatically initialize a controller
     * based on the route's namespace
     *
     * @var boolean
     * @access protected
     */
    protected $controllers_should_initialize = true;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param string $path      The path to traverse
     * @param Router $router    The router instance to attach
     * @access public
     */
    public function __construct($path, Router $router)
    {
        // Call our parent
        parent::__construct($path);

        $this->router = $router;
    }

    /**
     * Get the name of the top-level route
     *
     * @access public
     * @return string
     */
    public function getTopLevelRoute()
    {
        return $this->top_level_route;
    }

    /**
     * Set the name of the top-level route
     *
     * The top-level route is a special route
     * that gets loaded like all the rest, but
     * doesn't get namespaced... allowing a developer
     * to put a "master" route file in the same
     * directory as the other route files and still
     * use this loader without having to have the route
     * definitions be namespaced under a URL namespace
     *
     * @param string $top_level_route description
     * @access public
     * @return RouteLoader
     */
    public function setTopLevelRoute($top_level_route)
    {
        $this->top_level_route = (string) $top_level_route;

        return $this;
    }

    /**
     * Check whether controllers will initialize or not
     *
     * @access public
     * @return boolean
     */
    public function getControllersShouldInit()
    {
        return $this->controllers_should_initialize;
    }

    /**
     * Set whether controllers should be initialized
     * for each namespaced route when the namespace
     * has been matched
     *
     * @param boolean $should
     * @access public
     * @return RouteLoader
     */
    public function setControllersShouldInit($should)
    {
        $this->controllers_should_initialize = (bool) $should;

        return $this;
    }

    /**
     * Is this a valid file to process?
     *
     * @param SplFileInfo $file
     * @access protected
     * @return boolean
     */
    protected function isValid(SplFileInfo $file)
    {
        // Do we have to even check our array?
        if (is_array($this->valid_routes) && !empty($this->valid_routes)) {
            $filename = $file->getFilename();
            $basename = $file->getBasename('.php');

            // If the file's basename or filename aren't in our list
            if (!in_array($filename, $this->valid_routes)
                && !in_array($basename, $this->valid_routes)) {

                return false;
            }
        }

        return parent::isValid($file);
    }

    /**
     * Process the file being loaded
     *
     * @param SplFileInfo $file     The file to process
     * @param mixed $load_return    A reference to the value to be returned by `load()`
     * @access protected
     * @return void
     */
    protected function processFile(SplFileInfo $file, &$load_return)
    {
        $basename = $file->getBasename('.php');
        $base_url = '/'. $basename;

        // Is this our top-level route?
        if ($basename === $this->getTopLevelRoute()) {
            // Set our base url to null (for the top level)
            $base_url = null;
        }

        // Should we attempt to register a controller for auto-initialization?
        if ($this->controllers_should_initialize) {
            // Setup our router to instantiate our controller when the namespace is matched
            $this->router->with(
                $base_url,
                function () use ($basename) {
                    $this->router->respond(
                        function ($request, $response, $service, $app, $router) use ($basename) {
                            $router->initializeController($basename);
                        }
                    )->setIsProtected(true); // Let's protect this callback from being wrapped
                }
            );
        }

        // Setup our router to include our route definitions under a URL namespace
        $this->router->with($base_url, $file->getPathname());
    }

    /**
     * Load the routes
     *
     * If an optional routes array is passed,
     * the loader will ignore all other files
     * it finds except for those that match the
     * basename of the values in the array
     *
     * Example:
     *  $loader = new RouteLoader('./routes');
     *
     *  // Load all of the files in the routes directory
     *  $loader->load();
     *
     *  // Will only load the files matching 'users.php' and 'posts.php'
     *  $loader->load(['users', 'posts']);
     *
     * @param array $routes
     * @access public
     * @return mixed
     */
    public function load(array $routes = null)
    {
        // Set our valid routes
        $this->valid_routes = $routes;

        // Call our parent loader
        try {
            $returned = parent::load();
        } catch (UnexpectedValueException $e) {
            // Rethrow
            throw new RouteAutoLoadFailureException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $returned;
    }
}
