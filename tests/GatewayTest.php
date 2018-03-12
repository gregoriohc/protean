<?php

namespace Gregoriohc\Protean\Tests;

use Gregoriohc\Protean\Common\Exceptions\InvalidParametersException;
use Gregoriohc\Protean\Protean;
use Gregoriohc\Protean\Tests\Mocking\FakeGateway;

class GatewayTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function can_create_gateway_with_parameters()
    {
        /** @var FakeGateway $gateway */
        $gateway = Protean::create(FakeGateway::class, [
            'test_mode' => true,
            'api_key' => 'qwerty12345',
        ]);

        $this->assertInstanceOf(FakeGateway::class, $gateway);
        $this->assertEquals('FakeGateway', $gateway->shortName());
        $this->assertEquals('Fake', $gateway->name());
        $this->assertTrue($gateway->supportsPing());
        $this->assertFalse($gateway->supportsPong());
        $this->assertFalse($gateway->supports('other'));
        $this->assertFalse($gateway->supportsAcceptNotification());
        $this->assertEquals([
            'test_mode' => true,
            'api_key' => 'qwerty12345',
        ], $gateway->allParameters());
        $this->assertTrue($gateway->isInTestMode());
        $this->assertTrue(isset($gateway->api_key));
        $this->assertEquals('qwerty12345', $gateway->api_key);
    }

    /**
     * @test
     */
    public function fails_creating_gateway_without_required_parameters()
    {
        $this->expectException(InvalidParametersException::class);
        $this->expectExceptionMessage("'FakeGateway' validation failed: The api key field is required.");

        /** @var FakeGateway $gateway */
        $gateway = Protean::create(FakeGateway::class);
        $gateway->ping([]);
    }

    /**
     * @test
     */
    public function can_update_gateway_parameters()
    {
        /** @var FakeGateway $gateway */
        $gateway = Protean::create(FakeGateway::class, [
            'api_key' => 'qwerty12345',
        ]);

        $gateway->test_mode = true;
        $this->assertTrue($gateway['test_mode']);
        $this->assertTrue(isset($gateway->test_mode));

        $gateway['api_key'] = 'abcd1234';
        $this->assertEquals('abcd1234', $gateway['api_key']);
        $this->assertTrue(isset($gateway['api_key']));
        unset($gateway['api_key']);
        $this->assertFalse(isset($gateway['api_key']));

        $this->assertEquals([
            'test_mode' => true,
        ], json_decode(json_encode($gateway), true));

        foreach ($gateway as $parameter => $value) {
            $this->arrayHasKey($parameter, $gateway->allParameters());
        }
    }

    /**
     * @test
     */
    public function fails_creating_non_existing_gateway()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Gateway class 'Gregoriohc\Protean\NonExisting\Gateway' does not exists");

        Protean::create('NonExisting');
    }

    /**
     * @test
     */
    public function fails_calling_unsupported_gateway_method()
    {
        /** @var FakeGateway $gateway */
        $gateway = Protean::create(FakeGateway::class, [
            'api_key' => 'qwerty12345',
        ]);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage("Gateway 'FakeGateway' does not support 'pong' method");

        $gateway->pong();
    }

    /**
     * @test
     */
    public function fails_calling_non_existing_gateway_method()
    {
        /** @var FakeGateway $gateway */
        $gateway = Protean::create(FakeGateway::class, [
            'api_key' => 'qwerty12345',
        ]);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage("Method 'Gregoriohc\Protean\Tests\Mocking\FakeGateway::foo' does not exists");

        $gateway->foo();
    }
}
