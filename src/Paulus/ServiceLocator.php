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

namespace Paulus;

use BadMethodCallException;
use Klein\DataCollection\DataCollection;
use Paulus\Exception\DuplicateServiceException;

/**
 * ServiceLocator
 *
 * A service locator class used for easily
 * sharing dependencies through the app
 * in a more structured form.
 *
 * Not to be confused with a true Dependency Injection Container
 * (http://blog.ircmaxell.com/2012/08/object-scoping-triste-against-service.html)
 *
 * @uses	DataCollection
 * @package Paulus
 */
class ServiceLocator extends DataCollection
{

    /**
     * Constants
     */

    /**
     * The prefix used when aliasing
     * callable services
     *
     * @const string
     */
    const SERVICE_GETTER_PREFIX = 'get';


    /**
     * Properties
     */

    /**
     * An array of all of the services
     * that have been formally "registered"
     *
     * @var array
     * @access protected
     */
    protected $registered = [];


    /**
     * Methods
     */

    /**
     * Check if a given service name is in
     * fact a "registered" service
     *
     * @param string $name
     * @access public
     * @return boolean
     */
    public function isRegisteredService($name)
    {
        return in_array($name, $this->registered);
    }

    /**
     * Make sure that a service hasn't already
     * been registered under the given name
     *
     * @param string $name
     * @throws DuplicateServiceException    If the service is already registered
     * @access protected
     * @return ServiceLocator
     */
    protected function validateRegisteredService($name)
    {
        if (isset($this->attributes[$name]) && $this->isRegisteredService($name)) {
            throw new DuplicateServiceException('A service is already registered under '. $name);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name
     * @param mixed $default
     * @access public
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if ($this->isRegisteredService($name)) {
            return $this->attributes[$name]();
        }

        return parent::get($name, $default);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name
     * @param mixed $value
     * @access public
     * @return ServiceLocator
     */
    public function set($name, $value)
    {
        // Make sure we don't overwrite a service
        $this->validateRegisteredService($name);

        return parent::set($name, $value);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $attributes
     * @access public
     * @return ServiceLocator
     */
    public function replace(array $attributes = [])
    {
        // Clear our registered services
        $this->registered = [];

        return parent::replace($attributes);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $attributes
     * @param boolean $hard
     * @access public
     * @return ServiceLocator
     */
    public function merge(array $attributes = [], $hard = false)
    {
        // Clear any registered services that are being overwritten
        $this->registered = array_diff($this->registered, array_keys($attributes));

        return parent::merge($attributes, $hard);
    }

    /**
     * Register a lazy service
     *
     * @param string $name                  The name of the service
     * @param callable $closure             The callable function to execute when requesting our service
     * @access public
     * @return mixed
     */
    public function register($name, callable $closure)
    {
        // Make sure we don't overwrite a service
        $this->validateRegisteredService($name);

        $this->attributes[$name] = function () use ($closure) {
            static $instance;

            if (null === $instance) {
                $instance = $closure();
            }

            return $instance;
        };

        // Mark it as a registered service
        $this->registered[] = $name;

        return $this->attributes[$name];
    }

    /**
     * Magic call method for calling services
     *
     * @param string $method
     * @param array $args
     * @access public
     * @return mixed
     */
    public function __call($method, array $args)
    {
        // Strip the getter prefix
        if (strpos($method, static::SERVICE_GETTER_PREFIX) === 0) {
            $method = substr($method, strlen(static::SERVICE_GETTER_PREFIX));
        }

        if (!isset($this->attributes[$method]) || !is_callable($this->attributes[$method])) {
            throw new BadMethodCallException('Unknown or un-callable service '. $method .'()');
        }

        return call_user_func_array($this->attributes[$method], $args);
    }
}
