<?php

namespace Gregoriohc\Protean\Common\Messages;

interface ResponseInterface extends MessageInterface
{
    /**
     * Get the original request which generated this response
     *
     * @return RequestInterface
     */
    public function request();

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful();

    /**
     * Is the transaction cancelled by the user?
     *
     * @return boolean
     */
    public function isCancelled();
}