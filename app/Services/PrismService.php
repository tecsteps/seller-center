<?php

namespace App\Services;

use EchoLabs\Prism\Enums\Provider;
use EchoLabs\Prism\Prism;
use EchoLabs\Prism\Schema\SchemaInterface;
use EchoLabs\Prism\Structured\Generator as StructuredGenerator;

class PrismService
{
    public function __construct(
        private readonly string $model = 'SMALL_MODEL'
    ) {}

    public function structured(): StructuredGenerator
    {
        return Prism::structured()
            ->using(Provider::OpenAI, getenv($this->model));
    }
}
