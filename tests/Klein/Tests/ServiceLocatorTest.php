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

use Closure;
use Klein\DataCollection\DataCollection;
use Paulus\ServiceLocator;
use stdClass;

/**
 * ServiceLocatorTest
 *
 * @uses	AbstractPaulusTest
 * @package Paulus\Tests
 */
class ServiceLocatorTest extends AbstractPaulusTest
{

    public function testGetSetService()
    {
        $test_service_name = 'db';
        $test_service = new stdClass();

        $container = new ServiceLocator();

        $this->assertNull($container->get($test_service_name));

        $container->set($test_service_name, $test_service);

        $this->assertSame(
            $test_service,
            $container->get($test_service_name)
        );
    }

    public function testGetSetServiceViaProperty()
    {
        $test_service_name = 'db';
        $test_service = new stdClass();

        $container = new ServiceLocator();

        $this->assertNull($container->$test_service_name);

        $container->$test_service_name = $test_service;

        $this->assertSame(
            $test_service,
            $container->$test_service_name
        );
    }

    public function testGetSetServiceViaArrayAccess()
    {
        $test_service_name = 'db';
        $test_service = new stdClass();

        $container = new ServiceLocator();

        $this->assertNull($container[$test_service_name]);

        $container[$test_service_name] = $test_service;

        $this->assertSame(
            $test_service,
            $container[$test_service_name]
        );
    }

    public function testGetSetServiceWrappedInCallableDoesntInvoke()
    {
        $test_service_name = 'db';
        $test_service = function () {
            return new stdClass();
        };

        $container = new ServiceLocator();

        $this->assertNull($container->get($test_service_name));

        $container->set($test_service_name, $test_service);

        $received_service = $container->get($test_service_name);

        $this->assertSame(
            $test_service,
            $received_service
        );
        $this->assertTrue($received_service instanceof Closure);
    }

    public function testRegisterServiceDoesInvoke()
    {
        $test_service_name = 'db';
        $test_service = function () {
            return new stdClass();
        };

        $container = new ServiceLocator();

        $this->assertNull($container->get($test_service_name));

        $container->register($test_service_name, $test_service);

        $received_service = $container->get($test_service_name);

        $this->assertEquals(
            $test_service(),
            $received_service
        );
        $this->assertTrue($received_service instanceof stdClass);

        return $container;
    }

    public function testCallServiceDoesInvoke()
    {
        $test_service_name = 'db';
        $test_service = function () {
            return new stdClass();
        };

        $container = new ServiceLocator();

        $this->assertNull($container->get($test_service_name));

        $container->set($test_service_name, $test_service);

        $received_service = $container->$test_service_name();

        $this->assertEquals(
            $test_service(),
            $received_service
        );
        $this->assertTrue($received_service instanceof stdClass);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testCallBadServiceThrowsException()
    {
        $test_service_name = 'db';

        $container = new ServiceLocator();

        $this->assertNull($container->get($test_service_name));

        $container->$test_service_name();
    }

    public function testServiceIsCallableWithGetter()
    {
        $test_service_name = 'Mailer';
        $test_service = function () {
            return new stdClass();
        };

        $container = new ServiceLocator();

        $this->assertNull($container->get($test_service_name));

        $container->register($test_service_name, $test_service);

        $received_service = $container->$test_service_name();

        $this->assertEquals(
            $test_service(),
            $received_service
        );
        $this->assertTrue($received_service instanceof stdClass);

        $this->assertSame(
            $container->$test_service_name(),
            $container->{'get'. $test_service_name}()
        );
    }

    /**
     * @depends testRegisterServiceDoesInvoke
     * @expectedException Paulus\Exception\DuplicateServiceException
     */
    public function testRegisteredServiceOverwriteFails($container)
    {
        $test_service_name = 'db';
        $test_service = function () {
            return new DataCollection();
        };

        // Make sure we don't dirty the dependency
        $container = clone $container;

        // Attempt to overwrite the service
        $container->set($test_service_name, $test_service);
    }

    /**
     * @depends testRegisterServiceDoesInvoke
     */
    public function testRegisteredServiceOverwriteWorksAfterClear($container)
    {
        $test_service_name = 'db';
        $test_service = function () {
            return new DataCollection();
        };

        // Make sure we don't dirty the dependency
        $container = clone $container;

        $container->clear();

        $container->set($test_service_name, $test_service);

        $received_service = $container->get($test_service_name);

        $this->assertEquals(
            $test_service,
            $received_service
        );
        $this->assertTrue($received_service instanceof Closure);
    }

    /**
     * @depends testRegisterServiceDoesInvoke
     */
    public function testRegisteredServiceOverwriteWorksAfterMerge($container)
    {
        $test_service_name = 'db';
        $test_service = function () {
            return new DataCollection();
        };

        // Make sure we don't dirty the dependency
        $container = clone $container;

        $container->merge(['db' => new stdClass]);

        $container->set($test_service_name, $test_service);

        $received_service = $container->get($test_service_name);

        $this->assertEquals(
            $test_service,
            $received_service
        );
        $this->assertTrue($received_service instanceof Closure);
    }
}
