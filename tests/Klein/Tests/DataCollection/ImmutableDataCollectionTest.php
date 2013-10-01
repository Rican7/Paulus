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

namespace Paulus\Tests\DataCollection;

use Paulus\DataCollection\ImmutableDataCollection;
use Paulus\Tests\AbstractPaulusTest;

/**
 * ImmutableDataCollectionTest
 *
 * @uses    AbstractPaulusTest
 * @package Paulus\Tests\DataCollection
 */
class ImmutableDataCollectionTest extends AbstractPaulusTest
{

    /**
     * @expectedException LogicException
     */
    public function testSetThrowsException()
    {
        $collection = new ImmutableDataCollection();
        $collection->set('test', 'whatever');
    }

    /**
     * @expectedException LogicException
     */
    public function testReplaceThrowsException()
    {
        $collection = new ImmutableDataCollection();
        $collection->replace([]);
    }

    /**
     * @expectedException LogicException
     */
    public function testMergeThrowsException()
    {
        $collection = new ImmutableDataCollection();
        $collection->merge([]);
    }

    /**
     * @expectedException LogicException
     */
    public function testRemoveThrowsException()
    {
        $collection = new ImmutableDataCollection();
        $collection->remove('test');
    }
}
