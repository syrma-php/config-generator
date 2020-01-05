<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Generator\Processor;

use Syrma\ConfigGenerator\Generator\GeneratorContext;

interface PostProcessorInterface
{
    public function isSupported(GeneratorContext $context): bool;

    public function process(string $content, GeneratorContext $context): string;
}
