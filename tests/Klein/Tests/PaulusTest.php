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
}
