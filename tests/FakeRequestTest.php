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

        /** @var FakeRequest $request */
        $request = $gateway->fake([
            'foo' => new \stdClass(),
            'bar' => new \stdClass(),
        ]);
        $this->assertInstanceOf(FakeRequest::class, $request);

        $this->expectException(InvalidParametersException::class);
        $this->expectExceptionMessage("'FakeRequest' validation failed: The foo must be a model of type AbstractModel.");

        $request->validateParameters();
    }
}
