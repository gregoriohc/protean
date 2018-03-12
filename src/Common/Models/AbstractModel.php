<?php

namespace Gregoriohc\Protean\Common\Models;

use ArrayAccess;
use Gregoriohc\Protean\Common\Concerns\Parametrizable;
use IteratorAggregate;
use JsonSerializable;

abstract class AbstractModel implements ArrayAccess, IteratorAggregate, JsonSerializable
{
    use Parametrizable;

    /**
     * AbstractModel constructor.
     *
     * @param $parameters
     */
    public function __construct($parameters)
    {
        $this->bootParameters($parameters);
    }
}