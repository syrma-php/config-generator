<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Generator\HeaderGenerator;

use Syrma\ConfigGenerator\Generator\GeneratorContext;

interface HeaderGeneratorInterface
{
    public function isSupported(GeneratorContext $context): bool;

    public function generateHeader(string $configContent, GeneratorContext $context): string;

    public function isModified(string $oldConfigFileContent, GeneratorContext $context): bool;
}
