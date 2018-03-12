<?php

namespace Gregoriohc\Protean\Tests\Mocking;

use Gregoriohc\Protean\Common\AbstractGateway;
use Gregoriohc\Protean\Tests\Mocking\Messages\FakeRequest;

/**
 * @method bool supportsPing()
 * @method bool supportsPong()
 * @method bool supportsAcceptNotification()
 */
class FakeGateway extends AbstractGateway
{
    protected $requestMethods = [
        'ping',
        'pong'
    ];

    protected $webhookMethods = [
        'acceptNotification',
    ];

    /**
     * @return array
     */
    public function parametersValidationRules()
    {
        return array_merge(parent::parametersValidationRules(), [
            'api_key' => 'required',
        ]);
    }

    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     *
     * @return string
     */
    public function name()
    {
        return 'Fake';
    }

    /**
     * Boot the gateway client
     */
    public function bootClient()
    {
        $this->client = new \stdClass();
    }

    /**
     * @param array $parameters
     * @return \Gregoriohc\Protean\Common\Messages\AbstractRequest
     */
    public function fake($parameters = [])
    {
        return $this->createRequest(FakeRequest::class, $parameters);
    }
}
