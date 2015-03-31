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

namespace Paulus\Tests\Exception\Http;

use Exception;
use Paulus\Exception\Http\Standard\BadGateway;
use Paulus\Exception\Http\Standard\BadRequest;
use Paulus\Exception\Http\Standard\Forbidden;
use Paulus\Exception\Http\ApiExceptionFactory;
use Paulus\Exception\Http\Standard\InternalServerError;
use Paulus\Exception\Http\Standard\MethodNotAllowed;
use Paulus\Exception\Http\Standard\NotAcceptable;
use Paulus\Exception\Http\Standard\NotFound;
use Paulus\Exception\Http\Standard\NotImplemented;
use Paulus\Exception\Http\Standard\Unauthorized;
use Paulus\Tests\AbstractPaulusTest;

/**
 * ApiExceptionFactoryTest
 *
 * @uses    AbstractPaulusTest
 * @package Paulus\Tests\Exception\Http
 */
class ApiExceptionFactoryTest extends AbstractPaulusTest
{

    /**
     * Helpers
     */

    protected function getTestData()
    {
        return [
            400 => BadRequest::create(),
            401 => Unauthorized::create(),
            403 => Forbidden::create(),
            404 => NotFound::create(),
            405 => MethodNotAllowed::create(),
            406 => NotAcceptable::create(),
            500 => InternalServerError::create(),
            501 => NotImplemented::create(),
            502 => BadGateway::create(),
        ];
    }

    /**
     * Tests
     */

    public function testCreateFromCode()
    {
        $message = '';

        foreach ($this->getTestData() as $code => $exception) {
            $created_exception = ApiExceptionFactory::createFromCode($code);

            $this->assertEquals($code, $created_exception->getCode());
            $this->assertEquals($code, $created_exception->getDefaultCode());
            $this->assertEquals($message, $created_exception->getMessage());
            $this->assertEquals($message, $created_exception->getDefaultMessage());
            $this->assertTrue($created_exception instanceof $exception);
            $this->assertNull($created_exception->getPrevious());
        }
    }

    public function testCreateFromCodeWithMessage()
    {
        foreach ($this->getTestData() as $code => $exception) {
            $message = 'Throwing Message with Code: ' . $code;

            $created_exception = ApiExceptionFactory::createFromCode($code, $message);

            $this->assertEquals($code, $created_exception->getCode());
            $this->assertEquals($code, $created_exception->getDefaultCode());
            $this->assertEquals($message, $created_exception->getMessage());
            $this->assertNotEquals($message, $created_exception->getDefaultMessage());
            $this->assertTrue($created_exception instanceof $exception);
            $this->assertNull($created_exception->getPrevious());
        }
    }

    public function testCreateFromCodeWithMessageAndPreviousException()
    {
        $thrown_exception = new Exception("An error occurred!");

        foreach ($this->getTestData() as $code => $exception) {
            $message = 'Throwing Message with Code: ' . $code;

            $created_exception = ApiExceptionFactory::createFromCode($code, $message, $thrown_exception);

            $this->assertEquals($code, $created_exception->getCode());
            $this->assertEquals($code, $created_exception->getDefaultCode());
            $this->assertEquals($message, $created_exception->getMessage());
            $this->assertNotEquals($message, $created_exception->getDefaultMessage());
            $this->assertTrue($created_exception instanceof $exception);
            $this->assertNotNull($created_exception->getPrevious());
            $this->assertTrue($created_exception->getPrevious() instanceof $thrown_exception);
            $this->assertEquals($thrown_exception, $created_exception->getPrevious());
        }
    }
}
