<?php

namespace Gregoriohc\Protean;

use Gregoriohc\Protean\Common\GatewayFactory;
use Gregoriohc\Protean\Common\GatewayInterface;

/**
 * @method static GatewayInterface create($name, $parameters = [])
 * @mehtod
 *
 * @see GatewayFactory
 */
class Protean
{
    /**
     * Internal factory storage
     *
     * @var GatewayFactory
     */
    private static $factory;

    /**
     * Get the gateway factory
     *
     * Creates a new empty GatewayFactory if none has been set previously.
     *
     * @return GatewayFactory A GatewayFactory instance
     */
    public static function factory()
    {
        if (is_null(self::$factory)) {
            self::$factory = new GatewayFactory(__NAMESPACE__);
        }

        return self::$factory;
    }

    /**
     * Static function call router.
     *
     * All other function calls to the Protean class are routed to the factory.
     *
     * Example:
     *
     * <code>
     *   // Create a gateway
     *   $gateway = Protean::create('Something');
     * </code>
     *
     * @param string $method     The factory method to invoke.
     * @param array  $parameters Parameters passed to the factory method.
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array([self::factory(), $method], $parameters);
    }
}
