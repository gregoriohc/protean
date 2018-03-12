<?php

namespace Gregoriohc\Protean\Common;

use ArrayAccess;
use Gregoriohc\Protean\Common\Concerns\Parametrizable;
use IteratorAggregate;
use JsonSerializable;

/**
 * @property bool test_mode
 */
abstract class AbstractGateway implements GatewayInterface, ArrayAccess, IteratorAggregate, JsonSerializable
{
    use Parametrizable;

    protected $requestMethods = [
    ];

    protected $webhookMethods = [
    ];

    /**
     * @var mixed
     */
    protected $client;

    /**
     * Create a new gateway instance
     *
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        $this->bootParameters($parameters);
        $this->bootClient();
    }

    /**
     * @return array
     */
    public function defaultParameters()
    {
        return [
            'test_mode' => false,
        ];
    }

    /**
     * @return array
     */
    public function parametersValidationRules()
    {
        return [
            'test_mode' => 'required',
        ];
    }

    /**
     * Get the short name of the Gateway
     *
     * @return string
     */
    public function shortName()
    {
        return class_basename($this);
    }

    /**
     * @return boolean
     */
    public function isInTestMode()
    {
        return $this->test_mode;
    }

    /**
     * @return mixed
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Create and initialize a request object
     *
     * @param string $name The request name or class
     * @param array $parameters
     * @return \Gregoriohc\Protean\Common\Messages\AbstractRequest
     */
    protected function createRequest($name, $parameters = [])
    {
        $requestClass = $name;
        if (!str_contains($requestClass, '\\')) {
            $requestClass = $this->requestClass($requestClass);
        }

        if (!class_exists($requestClass)) {
            $gatewayClass = class_basename($this);
            throw new \BadMethodCallException("Gateway '$gatewayClass' does not support '$name' method");
        }

        $this->validateParameters();

        return new $requestClass($this, $parameters);
    }

    /**
     * @param string $name
     * @return string
     */
    private function requestClass($name)
    {
        return substr(get_class($this), 0, -strlen(class_basename($this))) . 'Messages\\' . ucfirst($name) . 'Request';
    }

    public function supports($name)
    {
        if (in_array($name, $this->requestMethods)) {
            return method_exists($this, $name) || class_exists($this->requestClass($name));
        } elseif (in_array($name, $this->webhookMethods)) {
            return method_exists($this, $name);
        }

        return false;
    }

    /**
     * @param string $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, 'supports') === 0) {
            return $this->supports(lcfirst(substr($name, 8)));
        }

        if (in_array($name, $this->requestMethods)) {
            $parameters = array_key_exists(0, $arguments) ? $arguments[0] : [];
            return $this->createRequest($name, $parameters);
        }

        $class = get_class($this);
        throw new \BadMethodCallException("Method '$class::$name' does not exists");
    }
}