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

use PHPUnit_Framework_TestCase;
use Paulus\Logger\UnitTestLogger;
use Paulus\Paulus;

/**
 * AbstractPaulusTest
 *
 * Abstract base test class for making
 * unit testing Paulus less of a pain
 *
 * @uses	PHPUnit_Framework_TestCase
 * @abstract
 * @package Paulus\Tests
 */
abstract class AbstractPaulusTest extends PHPUnit_Framework_TestCase
{

    /**
     * Properties
     */

    /**
     * The directory containing the test files
     *
     * @var string
     * @access private
     */
    private $tests_dir;

    /**
     * The automatically created test Paulus instance
     * (for easy testing and less boilerplate)
     *
     * @var Paulus\Paulus
     * @access protected
     */
    protected $paulus_app;


    /**
     * Methods
     */

    /**
     * Setup our test
     *
     * @access protected
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        // Create a paulus instance, since we're going to need it EVERYWHERE
        $this->paulus_app = $this->getPaulusWithTestLogger();

        // Get our tests directory
        $this->tests_dir = dirname($GLOBALS['__PHPUNIT_BOOTSTRAP']);
    }

    /**
     * Get a Paulus instance with a logger
     * designed for unit testing
     *
     * @access protected
     * @return Paulus
     */
    protected function getPaulusWithTestLogger()
    {
        return new Paulus(
            null,
            null,
            new UnitTestLogger()
        );
    }

    /**
     * Get the directory containing our tests
     *
     * @access protected
     * @return string
     */
    protected function getTestsDir()
    {
        return $this->tests_dir;
    }
}
