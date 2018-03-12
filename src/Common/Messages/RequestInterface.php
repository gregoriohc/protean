<?php

namespace Gregoriohc\Protean\Common\Messages;

interface RequestInterface extends MessageInterface
{
    /**
     * Get the response class for this request
     *
     * @return string
     */
    public function responseClass();

    /**
     * Get the response to this request (if the request has been sent)
     *
     * @return ResponseInterface
     */
    public function response();

    /**
     * Send the request
     *
     * @return ResponseInterface
     */
    public function send();

    /**
     * Send the request with specified data, and return the response.
     *
     * @param  array $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data);
}
