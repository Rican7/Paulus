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

namespace Paulus\Tests\Response;

use Paulus\Response\ApiResponse;
use Paulus\Tests\AbstractPaulusTest;

/**
 * ApiResponseTest
 *
 * @uses    AbstractPaulusTest
 * @package Paulus\Tests\Response
 */
class ApiResponseTest extends AbstractPaulusTest
{

    /**
     * Helpers
     */

    protected function getTestData()
    {
        return [
            'name' => 'Trevor Suarez',
            'copyright' => '2013 Trevor Suarez',
            'version' => [
                'major' => 2,
                'minor' => 0,
                'patch' => 0,
            ],
        ];
    }


    /**
     * Tests
     */

    public function testConstructor()
    {
        // Test data
        $test_data = $this->getTestData();
        $test_status_code = 304;
        $test_headers = [
            'x-whatever' => 'yup',
        ];

        $response = new ApiResponse($test_data, $test_status_code, $test_headers);

        $this->assertSame($test_data, $response->getData());
        $this->assertSame($test_status_code, $response->code());
        $this->assertEquals($test_headers, $response->headers()->all(array_keys($test_headers)));
        $this->assertFalse($response->isLocked());
    }

    public function testGetSetData()
    {
        // Test data
        $test_data = $this->getTestData();

        $response = new ApiResponse();

        $this->assertNull($response->getData());

        $old_body = $response->body();
        $response->unlock()->setData($test_data);
        $new_body = $response->body();

        $this->assertSame($test_data, $response->getData());
        $this->assertNotSame($old_body, $new_body);
    }

    public function testGetSetStatusSlug()
    {
        // Test data
        $test_status_slug = 'HAI!';

        $response = new ApiResponse();

        // Make sure we have a default that matches the HTTP Status Code
        $this->assertNotNull($response->getStatusSlug());
        $this->assertSame($response->status()->getMessage(), $response->getStatusSlug());

        $old_body = $response->body();
        $response->unlock()->setStatusSlug($test_status_slug);
        $new_body = $response->body();

        $this->assertSame($test_status_slug, $response->getStatusSlug());
        $this->assertNotSame($old_body, $new_body);
    }

    public function testGetSetMessage()
    {
        // Test message
        $test_message = 'human readable!';

        $response = new ApiResponse();

        $this->assertNull($response->getMessage());

        $old_body = $response->body();
        $response->unlock()->setMessage($test_message);
        $new_body = $response->body();

        $this->assertSame($test_message, $response->getMessage());
        $this->assertNotSame($old_body, $new_body);
    }

    public function testGetSetMoreInfo()
    {
        // Test data
        $test_more_info = (object) [
            'thing' => 'bob',
            'other_thing' => [
                'has',
                'many',
                'things',
            ],
        ];

        $response = new ApiResponse();

        // Make sure we have a default that matches the HTTP Status Code
        $this->assertNull($response->getMoreInfo());

        $old_body = $response->body();
        $response->unlock()->setMoreInfo($test_more_info);
        $new_body = $response->body();

        $this->assertSame($test_more_info, $response->getMoreInfo());
        $this->assertNotSame($old_body, $new_body);
    }
}
