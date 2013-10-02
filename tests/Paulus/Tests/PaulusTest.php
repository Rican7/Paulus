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

namespace Paulus\Tests;

use Klein\DataCollection\RouteCollection;
use Paulus\DataCollection\ImmutableDataCollection;
use Paulus\FileLoader\RouteLoaderFactory;
use Paulus\Paulus;
use Paulus\Router;
use Paulus\ServiceLocator;

/**
 * PaulusTest
 *
 * @uses    AbstractPaulusTest
 * @package Paulus\Tests
 */
class PaulusTest extends AbstractPaulusTest
{

    /**
     * Helpers
     */

    protected function getTestRoutesPath()
    {
        return $this->getTestsDir() .'/../'. RouteLoaderFactory::ROUTE_DIR_NAME;
    }

    protected function tearDown()
    {
        // Clean up
        @rmdir($this->getTestRoutesPath());

        parent::tearDown();
    }


    /**
     * Tests
     */

    public function testGetStartTime()
    {
        $start_time = $this->paulus_app->getStartTime();
        $testing_time = microtime(true);
        $diff = $testing_time - $start_time;

        $this->assertTrue(is_float($start_time));

        // These should never be the exact SAME
        $this->assertNotSame(microtime(true), $start_time);

        // Make sure the time isn't wildly different
        $this->assertTrue($diff < 10000);
    }

    public function testRouter()
    {
        $router = $this->paulus_app->router();

        $this->assertTrue($router instanceof Router);
    }

    public function testLocator()
    {
        $locator = $this->paulus_app->locator();

        $this->assertTrue($locator instanceof ServiceLocator);
    }

    public function testPrepareWithoutAutoLoading()
    {
        $returned = $this->paulus_app->prepare(false);

        $this->assertTrue($returned instanceof Paulus);
    }

    /**
     * @expectedException Paulus\Exception\UnableToInferRouteDirectoryException
     */
    public function testPrepareWithAutoLoadingFailsToLoad()
    {
        $this->paulus_app->prepare(true);
    }

    public function testPrepareWithRouteInfer()
    {
        // Make a directory that it can find
        mkdir($this->getTestRoutesPath(), 0777);

        $returned = $this->paulus_app->prepare(true);

        $this->assertTrue($returned instanceof Paulus);

        return $this->paulus_app;
    }

    /**
     * @depends testPrepareWithRouteInfer
     * @expectedException Paulus\Exception\AlreadyPreparedException
     */
    public function testMultiplePrepareThrowsException($app)
    {
        $app->prepare();
    }

    public function testCallUnknownMethodRedirectsToRouter()
    {
        $returned = $this->paulus_app->routes();

        $this->assertTrue($returned instanceof RouteCollection);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testCallBadMethodThrowsAnException()
    {
        $this->paulus_app->thisMethodShouldNotExistANYWHERE();
    }
}
