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

namespace Paulus\Tests\FileLoader;

use Paulus\FileLoader\RouteLoader;
use Paulus\Router;
use Paulus\Tests\AbstractPaulusTest;

/**
 * RouteLoaderTest
 *
 * @uses    AbstractPaulusTest
 * @package Paulus\Tests\FileLoader
 */
class RouteLoaderTest extends AbstractPaulusTest
{

    /**
     * Helpers
     */

    protected function getTestRoutesPath()
    {
        return $this->getTestsDir() .'/test_routes/';
    }

    protected function getTestRouteLoader(Router $router = null)
    {
        return new RouteLoader(
            $this->getTestRoutesPath(),
            $router ?: new Router()
        );
    }


    /**
     * Tests
     */

    public function testGetSetTopLevelRoute()
    {
        // Test data
        $top_level_route = 'dog-cheese';

        $loader = $this->getTestRouteLoader();

        $initial = $loader->getTopLevelRoute();
        $this->assertNotSame($initial, $top_level_route);

        $loader->setTopLevelRoute($top_level_route);
        $new = $loader->getTopLevelRoute();
        $this->assertSame($top_level_route, $new);
    }

    public function testGetSetControllersShouldInit()
    {
        // Test data
        $controllers_should_init = false;

        $loader = $this->getTestRouteLoader();

        $initial = $loader->getControllersShouldInit();
        $this->assertNotSame($initial, $controllers_should_init);

        $loader->setControllersShouldInit($controllers_should_init);
        $new = $loader->getControllersShouldInit();
        $this->assertSame($controllers_should_init, $new);
    }

    public function testLoad()
    {
        $router = new Router();
        $loader = $this->getTestRouteLoader($router);

        $this->assertEmpty($router->routes()->all());
        $this->assertTrue(count($router->routes()) === 0);

        $loader->load();

        $this->assertNotEmpty($router->routes()->all());
        $this->assertTrue(count($router->routes()) > 0);

        return $router;
    }

    /**
     * @depends testLoad
     */
    public function testLoadSpecificRoutes($previous_router)
    {
        // Test data
        $routes_to_load = [
            'users',
            'posts',
        ];

        $router = new Router();
        $loader = $this->getTestRouteLoader($router);

        $this->assertEmpty($router->routes()->all());
        $this->assertTrue(count($router->routes()) === 0);

        $loader->load($routes_to_load);

        $this->assertNotEmpty($router->routes()->all());
        $this->assertTrue(count($router->routes()) > 0);

        $this->assertNotSame(
            count($previous_router->routes()),
            count($router->routes())
        );
    }

    public function testLoadSpecificRoutesThatDontExist()
    {
        // Test data
        $routes_to_load = [
            'this_shouldnt_exist',
            'dude-bro-and-stuff',
        ];

        $router = new Router();
        $loader = $this->getTestRouteLoader($router);

        $this->assertEmpty($router->routes()->all());
        $this->assertTrue(count($router->routes()) === 0);

        $loader->load($routes_to_load);

        // Should still be empty
        $this->assertEmpty($router->routes()->all());
        $this->assertTrue(count($router->routes()) === 0);
    }
}
