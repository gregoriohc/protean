<?php

namespace Gregoriohc\Protean\Common\Concerns;

use ArrayIterator;
use Gregoriohc\Protean\Common\Exceptions\InvalidParametersException;
use Gregoriohc\Protean\Common\Validation\Validator;

trait Parametrizable
{
    /**
     * @var array
     */
    private $parameters = [];

    /**
     * Initialize request with parameters
     *
     * @param array $parameters
     */
    public function bootParameters($parameters = [])
    {
        $this->parameters = array_replace($this->defaultParameters(), $parameters);
    }

    /**
     * @return array
     */
    public function defaultParameters()
    {
        return [];
    }

    /**
     * @return array
     */
    public function parametersValidationRules()
    {
        return [];
    }

    /**
     * @param array $extraRules
     */
    public function validateParameters($extraRules = [])
    {
        $rules = array_merge($this->parametersValidationRules(), $extraRules);

        $validator = Validator::make($this->allParameters(), $rules, $this);

        if ($validator->fails()) {
            $messages = [];
            foreach ($validator->messages()->all() as $message) {
                $messages[] = $message;
            }
            $class = class_basename($this);
            throw new InvalidParametersException("'$class' validation failed: " . implode(' ', $messages));
        }
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getParameter($key, $default = null)
    {
        return array_get($this->parameters, $key, $default);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return array
     */
    public function setParameter($key, $value)
    {
        return array_set($this->parameters, $key, $value);
    }

    /**
     * @param string|array $keys
     * @return bool
     */
    public function hasParameter($keys)
    {
        return array_has($this->parameters, $keys);
    }

    /**
     * @param string|array $keys
     */
    public function unsetParameter($keys)
    {
        array_forget($this->parameters, $keys);
    }

    /**
     * @return array
     */
    public function allParameters()
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function parametersToArray()
    {
        $data = $this->parameters;

        foreach ($data as $key => $value) {
            if (is_object($value)) {
                if (in_array(Parametrizable::class, class_uses_recursive($value))) {
                    /** @var Parametrizable $value */
                    $data[$key] = $value->parametersToArray();
                } else {
                    $data[$key] = (array) $value;
                }
            }
        }

        return $data;
    }

    /**
     * @param $map
     * @param bool $skipMissing
     * @return array
     */
    public function mapParameters($map, $skipMissing = false)
    {
        $data = [];
        $parameters = $this->parametersToArray();

        foreach ($map as $mapKey => $parameter) {
            if ($skipMissing && !array_has($parameters, $parameter)) {
                continue;
            }
            array_set($data, $mapKey, array_get($parameters, $parameter));
        }

        return $data;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getParameter($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->setParameter($name, $value);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->hasParameter($name);
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasParameter($offset);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getParameter($offset);
    }

    /**
     * @param $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->setParameter($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->unsetParameter($offset);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->parameters);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->parameters;
    }
}