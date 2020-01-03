<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Generator\HeaderGenerator;

use Syrma\ConfigGenerator\Config\ConfigFileType;

class IniHeaderGenerator extends AbstractHeaderGenerator
{
    protected function getSupportedTypes(): array
    {
        return [
            ConfigFileType::TYPE_INI,
        ];
    }

    protected function wrapLine(string $line): string
    {
        return '; '.$line;
    }
}
