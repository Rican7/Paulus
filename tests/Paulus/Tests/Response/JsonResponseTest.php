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

use Paulus\Response\JsonResponse;
use Paulus\Tests\AbstractPaulusTest;

/**
 * JsonResponseTest
 *
 * @uses    AbstractPaulusTest
 * @package Paulus\Tests\Response
 */
class JsonResponseTest extends AbstractPaulusTest
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
            'X-Whatever' => 'yup',
        ];

        $response = new JsonResponse($test_data, $test_status_code, $test_headers);

        $this->assertSame($test_data, $response->getData());
        $this->assertSame($test_status_code, $response->code());
        $this->assertFalse($response->isLocked());

        $this->assertEquals(
            array_change_key_case($test_headers, CASE_LOWER),
            $response->headers()->all(
                array_keys(array_change_key_case($test_headers, CASE_LOWER))
            )
        );
    }

    public function testSetData()
    {
        // Test data
        $test_data = $this->getTestData();

        $response = new JsonResponse();

        // Make sure default data is an empty object
        $this->assertTrue($response->getData() instanceof \stdClass);
        $this->assertEmpty(get_object_vars($response->getData()));

        $response->unlock()->setData($test_data);

        $this->assertSame($test_data, $response->getData());

        // Make sure the body's encoded
        $this->assertSame(
            json_encode($test_data),
            $response->body()
        );
    }

    public function testContentHeaderIsntOverwritten()
    {
        // Test data
        $test_data = $this->getTestData();
        $test_status_code = 304;
        $test_headers = [
            'Content-Type' => 'image/png',
        ];

        $response = new JsonResponse($test_data, $test_status_code, $test_headers);

        $this->assertSame(
            $test_headers['Content-Type'],
            $response->headers()->get('content-type')
        );

        $this->assertEquals(
            array_change_key_case($test_headers, CASE_LOWER),
            array_change_key_case($response->headers()->all(), CASE_LOWER)
        );
    }
}
