<?php

namespace Gregoriohc\Protean\Common\Messages;

interface MessageInterface
{
    /**
     * Get the raw data array for this message.
     *
     * @return mixed
     */
    public function data();
}
