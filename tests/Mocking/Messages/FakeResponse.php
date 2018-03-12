<?php

namespace Gregoriohc\Protean\Tests\Mocking\Messages;

use Gregoriohc\Protean\Common\Messages\AbstractResponse;

class FakeResponse extends AbstractResponse
{
    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return count($this->data()) > 0;
    }
}
