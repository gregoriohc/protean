<?php

namespace Gregoriohc\Protean\Tests\Mocking\Models;

use Gregoriohc\Protean\Common\Models\AbstractModel;

class Something extends AbstractModel
{
    /**
     * @return array
     */
    public function parametersValidationRules()
    {
        return [
            'name' => 'required|string',
        ];
    }
}