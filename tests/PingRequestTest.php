<?php

namespace Gregoriohc\Protean\Tests;

use Gregoriohc\Protean\Common\Exceptions\InvalidParametersException;
use Gregoriohc\Protean\Protean;
use Gregoriohc\Protean\Tests\Mocking\Messages\PingRequest;
use Gregoriohc\Protean\Tests\Mocking\FakeGateway;
use Gregoriohc\Protean\Tests\Mocking\Messages\FakeResponse;
use Gregoriohc\Protean\Tests\Mocking\Models\Something;
use RuntimeException;

class PingRequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function can_ping_with_something()
    {
        /** @var FakeGateway $gateway */
        $gateway = Protean::create(FakeGateway::class, [
            'api_key' => 'qwerty12345',
        ]);
        $this->assertInstanceOf(FakeGateway::class, $gateway);
        $this->assertTrue($gateway->supportsPing());

        $something = new Something([
            'name' => 'Lorem impsum',
        ]);

        /** @var PingRequest $request */
        $request = $gateway->ping([
            'something' => $something,
            'foo' => 'foo',
            'bar' => 'bar',
        ]);
        $this->assertInstanceOf(PingRequest::class, $request);

        /** @var FakeResponse $response */
        $response = $request->send();
        $this->assertInstanceOf(FakeResponse::class, $response);

        $this->assertTrue($response->isSuccessful());

        $responseData = $response->data();
        $this->assertEquals([
            'something' => $something->parametersToArray(),
            'foo' => 'foo',
            'bar' => 'bar',
        ], $responseData);
    }

    /**
     * @test
     */
    public function fails_capturing_without_something_model()
    {
        /** @var FakeGateway $gateway */
        $gateway = Protean::create(FakeGateway::class, [
            'api_key' => 'qwerty12345',
        ]);

        $this->expectException(InvalidParametersException::class);
        $this->expectExceptionMessage("'PingRequest' validation failed: The something must be a model of type Gregoriohc\Protean\Tests\Mocking\Models\Something.");

        $gateway->ping([
            'something' => '1234',
            'foo' => 'foo',
            'bar' => 'bar',
        ])->send();
    }

    /**
     * @test
     */
    public function fails_capturing_with_no_valid_something()
    {
        /** @var FakeGateway $gateway */
        $gateway = Protean::create(FakeGateway::class, [
            'api_key' => 'qwerty12345',
        ]);

        $something = new Something([
        ]);

        $this->expectException(InvalidParametersException::class);
        $this->expectExceptionMessage("'Something' validation failed: The name field is required.");

        $gateway->ping([
            'something' => $something,
            'foo' => 'foo',
        ])->send();
    }

    /**
     * @test
     */
    public function fails_getting_response_before_send()
    {
        /** @var FakeGateway $gateway */
        $gateway = Protean::create(FakeGateway::class, [
            'api_key' => 'qwerty12345',
        ]);

        /** @var PingRequest $request */
        $request = $gateway->ping([
            'something' => new Something([
                'name' => 'Lorem impsum',
            ]),
            'foo' => 'foo',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("You must call send() before accessing the Response.");

        $request->response();
    }

    /**
     * @test
     */
    public function can_get_response_gateway_and_client()
    {
        /** @var FakeGateway $gateway */
        $gateway = Protean::create(FakeGateway::class, [
            'api_key' => 'qwerty12345',
        ]);

        /** @var PingRequest $request */
        $request = $gateway->ping([
            'something' => new Something([
                'name' => 'Lorem impsum',
            ]),
            'foo' => 'foo',
        ]);

        $request->send();

        $response = $request->response();
        $this->assertInstanceOf(FakeResponse::class, $response);

        $gateway = $request->gateway();
        $this->assertInstanceOf(FakeGateway::class, $gateway);

        $client = $request->gateway()->client();
        $this->assertInstanceOf('stdClass', $client);
    }
}
