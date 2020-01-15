<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Generator\HeaderGenerator;

use Syrma\ConfigGenerator\Config\ConfigFileType;

class PhpHeaderGenerator extends AbstractHeaderGenerator
{
    protected function getSupportedTypes(): array
    {
        return [
            ConfigFileType::TYPE_PHP,
        ];
    }

    protected function wrapLine(string $line): string
    {
        return '<?php /* '.$line.' */ ?>';
    }
}
