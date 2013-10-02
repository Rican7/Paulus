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
use Paulus\FileLoader\RouteLoaderFactory;
use Paulus\Router;
use Paulus\Tests\AbstractPaulusTest;

/**
 * RouteLoaderFactoryTest
 *
 * @uses    AbstractPaulusTest
 * @package Paulus\Tests
 */
class RouteLoaderFactoryTest extends AbstractPaulusTest
{

    /**
     * Helpers
     */

    protected function getTestRoutesPath()
    {
        return $this->getTestsDir() .'/../'. RouteLoaderFactory::ROUTE_DIR_NAME;
    }

    protected function getTestRoutesFile()
    {
        return rtrim($this->getTestRoutesPath(), '/');
    }

    protected function getTestRouter()
    {
        return new Router();
    }

    protected function tearDown()
    {
        // Clean up
        @rmdir($this->getTestRoutesPath());
        @unlink($this->getTestRoutesFile());

        parent::tearDown();
    }


    /**
     * Tests
     */

    public function testbuildByDirectoryInferring()
    {
        // Make a directory that it can find
        mkdir($this->getTestRoutesPath(), 0777);

        $returned = RouteLoaderFactory::buildByDirectoryInferring(
            $this->getTestRouter(),
            $this->getTestRoutesPath()
        );

        $this->assertTrue($returned instanceof RouteLoader);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testbuildByDirectoryInferringFails()
    {
        // Make sure the routes path is gone
        @rmdir($this->getTestRoutesPath());

        RouteLoaderFactory::buildByDirectoryInferring(
            $this->getTestRouter()
        );
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testbuildByDirectoryInferringFindsFile()
    {
        // Make sure the routes path is gone
        @rmdir($this->getTestRoutesPath());

        // Create a file with the same name
        touch($this->getTestRoutesFile());

        RouteLoaderFactory::buildByDirectoryInferring(
            $this->getTestRouter()
        );
    }
}
