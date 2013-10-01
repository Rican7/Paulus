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

use Paulus\FileArrayLoader;

/**
 * FileArrayLoaderTest
 *
 * @uses    AbstractPaulusTest
 * @package Paulus\Tests;
 */
class FileArrayLoaderTest extends AbstractPaulusTest
{

    /**
     * Helpers
     */

    protected function getTestFilePath()
    {
        return __DIR__ .'/../../file_array_loader_files/';
    }


    /**
     * Tests
     */

    public function testLoad()
    {
        $loader = new FileArrayLoader($this->getTestFilePath());

        $returned = $loader->load();

        $this->assertTrue(is_array($returned));
        $this->assertTrue(count($returned) > 0);

        return $returned;
    }

    /**
     * @depends testLoad
     */
    public function testLoadWithCustomIgnore($normal)
    {
        $test_file_path = $this->getTestFilePath() .'/more/';
        $test_ignore_prefix = 'ignore_';

        $loader = new FileArrayLoader($test_file_path, $test_ignore_prefix);

        $returned = $loader->load();

        $this->assertTrue(is_array($returned));
        $this->assertTrue(count($returned) > 0);

        $this->assertNotEquals($normal, $returned);
        $this->assertNotEquals(count($normal), count($returned));

        $this->assertArrayHasKey('_should_be_fine', $returned);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testLoadWithUncallable()
    {
        // Don't ignore any prefixes..
        $loader = new FileArrayLoader($this->getTestFilePath(), '');

        $returned = $loader->load();
    }
}
