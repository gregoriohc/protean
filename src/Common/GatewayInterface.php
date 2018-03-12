<?php

namespace Gregoriohc\Protean\Common;

interface GatewayInterface
{
    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     *
     * @return string
     */
    public function name();

    /**
     * Get gateway short name
     *
     * This name can be used with GatewayFactory as an alias of the gateway class,
     * to create new instances of this gateway.
     *
     * @return string
     */
    public function shortName();

    /**
     * Boot the gateway client
     */
    public function bootClient();
}
