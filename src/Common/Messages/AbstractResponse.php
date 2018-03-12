<?php

namespace Gregoriohc\Protean\Common\Messages;

abstract class AbstractResponse implements ResponseInterface
{

    /**
     * The embodied request object.
     *
     * @var RequestInterface
     */
    protected $request;

    /**
     * The data contained in the response.
     *
     * @var mixed
     */
    protected $data;

    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request.
     * @param mixed $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    /**
     * Get the initiating request object.
     *
     * @return RequestInterface
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Is the transaction cancelled by the user?
     *
     * @return boolean
     */
    public function isCancelled()
    {
        return false;
    }

    /**
     * Get the response data.
     *
     * @return mixed
     */
    public function data()
    {
        return $this->data;
    }
}
