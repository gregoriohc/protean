<?php

namespace Gregoriohc\Protean\Tests;

use Gregoriohc\Protean\Common\Exceptions\InvalidParametersException;
use Gregoriohc\Protean\Protean;
use Gregoriohc\Protean\Tests\Mocking\FakeGateway;
use Gregoriohc\Protean\Tests\Mocking\Messages\FakeRequest;

class FakeRequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function fails_validating_request()
    {
        /** @var FakeGateway $gateway */
        $gateway = Protean::create(FakeGateway::class, [
            'api_key' => 'qwerty12345',
        ]);

        $data = [
            'foo' => new \stdClass(),
            'bar' => new \stdClass(),
        ];

        /** @var FakeRequest $request */
        $request = $gateway->fake($data);
        $this->assertInstanceOf(FakeRequest::class, $request);

        $this->expectException(InvalidParametersException::class);
        $this->expectExceptionMessage("'FakeRequest' validation failed: The foo must be a model of type AbstractModel.");

        $request->validateParameters();
    }
    /**
     * @test
     */
    public function maps_parameters_to_data()
    {
        /** @var FakeGateway $gateway */
        $gateway = Protean::create(FakeGateway::class, [
            'api_key' => 'qwerty12345',
        ]);

        $data = [
            'foo' => new \stdClass(),
            'bar' => new \stdClass(),
        ];

        /** @var FakeRequest $request */
        $request = $gateway->fake($data);
        $this->assertInstanceOf(FakeRequest::class, $request);

        $this->assertEquals([
            'foo' => [],
            'bar' => [],
        ], $request->data());
    }
}
