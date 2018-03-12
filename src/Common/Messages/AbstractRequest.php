<?php

namespace Gregoriohc\Protean\Common\Messages;

use ArrayAccess;
use Gregoriohc\Protean\Common\Concerns\Parametrizable;
use IteratorAggregate;
use JsonSerializable;
use RuntimeException;

abstract class AbstractRequest implements RequestInterface, ArrayAccess, IteratorAggregate, JsonSerializable
{
    use Parametrizable;

    /**
     * The gateway
     *
     * @var \Gregoriohc\Protean\Common\AbstractGateway
     */
    protected $gateway;

    /**
     * The response to this request (if the request has been sent)
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * AbstractRequest constructor.
     * @param \Gregoriohc\Protean\Common\AbstractGateway $gateway
     * @param array $parameters
     */
    public function __construct($gateway, $parameters = [])
    {
        $this->gateway = $gateway;
        $this->bootParameters($parameters);
    }

    /**
     * @param array $data
     * @return ResponseInterface
     */
    protected function createResponse($data)
    {
        $responseClass = $this->responseClass();

        return $this->response = new $responseClass($this, $data);
    }

    /**
     * Get the response to this request (if the request has been sent)
     *
     * @return ResponseInterface
     */
    public function response()
    {
        if (null === $this->response) {
            throw new RuntimeException('You must call send() before accessing the Response.');
        }

        return $this->response;
    }

    /**
     * Send the request
     *
     * @return ResponseInterface
     */
    public function send()
    {
        $this->validateParameters();

        $data = $this->data();

        return $this->sendData($data);
    }

    /**
     * @return \Gregoriohc\Protean\Common\AbstractGateway
     */
    public function gateway()
    {
        return $this->gateway;
    }
}
