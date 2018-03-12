<?php

namespace Gregoriohc\Protean\Tests\Mocking\Messages;

use Gregoriohc\Protean\Common\Messages\AbstractRequest;
use Gregoriohc\Protean\Common\Messages\ResponseInterface;

class PingRequest extends AbstractRequest
{
    /**
     * @return array
     */
    public function parametersValidationRules()
    {
        return array_merge(parent::parametersValidationRules(), [
            'something' => 'required|is_model:Gregoriohc\Protean\Tests\Mocking\Models\Something|is_valid',
            'foo' => 'required',
            'bar' => '',
        ]);
    }

    /**
     * Get the raw data array for this message.
     *
     * @return mixed
     */
    public function data()
    {
        return $this->parametersToArray();
    }

    /**
     * Send the request with specified data, and return the response.
     *
     * @param  array $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        return $this->createResponse($data);
    }

    /**
     * Get the response class for this request
     *
     * @return string
     */
    public function responseClass()
    {
        return FakeResponse::class;
    }
}
