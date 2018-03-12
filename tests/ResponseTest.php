<?php

namespace Gregoriohc\Protean\Tests;

use Gregoriohc\Protean\Common\Models\Token;
use Gregoriohc\Protean\Protean;
use Gregoriohc\Protean\Tests\Mocking\Messages\PingRequest;
use Gregoriohc\Protean\Tests\Mocking\FakeGateway;
use Gregoriohc\Protean\Tests\Mocking\Messages\FakeResponse;
use Gregoriohc\Protean\Tests\Mocking\Models\Something;

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function can_check_response()
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

        /** @var FakeResponse $response */
        $response = $request->send();
        $this->assertInstanceOf(FakeResponse::class, $response);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isCancelled());

        $responseData = $response->data();
        $this->assertEquals([
            'something' => ['name' => 'Lorem impsum'],
            'foo' => 'foo',
        ], $responseData);

        $this->assertInstanceOf(PingRequest::class, $response->request());
    }
}
