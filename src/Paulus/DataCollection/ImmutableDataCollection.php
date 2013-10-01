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

namespace Paulus\DataCollection;

use Klein\DataCollection\DataCollection;
use LogicException;

/**
 * ImmutableDataCollection
 *
 * An extension of the Klein DataCollection
 * class that is immutable
 *
 * @uses    Klein\DataCollection\DataCollection
 * @package Paulus\DataCollection
 */
class ImmutableDataCollection extends DataCollection
{

    /**
     * Constants
     */

    const IMMUTABLE_MESSAGE = 'Illegal operation. Attempt to modify immutable collection.';


    /**
     * Methods
     */

    /**
     * Override and remove the set functionality
     *
     * @override (does not call parent)
     * @param string $key
     * @param mixed $value
     * @throws LogicException
     * @access public
     * @return void
     */
    public function set($key, $value)
    {
        throw new LogicException(self::IMMUTABLE_MESSAGE);
    }

    /**
     * Override and remove the replace functionality
     *
     * @override (does not call parent)
     * @param array $attributes
     * @throws LogicException
     * @access public
     * @return void
     */
    public function replace(array $attributes = [])
    {
        throw new LogicException(self::IMMUTABLE_MESSAGE);
    }

    /**
     * Override and remove the merge functionality
     *
     * @override (does not call parent)
     * @param array $attributes
     * @param boolean $hard
     * @throws LogicException
     * @access public
     * @return ServiceLocator
     */
    public function merge(array $attributes = [], $hard = false)
    {
        throw new LogicException(self::IMMUTABLE_MESSAGE);
    }

    /**
     * Override and remove the remove functionality
     *
     * @override (does not call parent)
     * @param array $key
     * @throws LogicException
     * @access public
     * @return void
     */
    public function remove($key)
    {
        throw new LogicException(self::IMMUTABLE_MESSAGE);
    }
}
