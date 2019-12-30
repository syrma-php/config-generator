<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Generator\HeaderGenerator;

use Syrma\ConfigGenerator\Definition\ConfigFileType;

class BlockCommentBaseHeaderGenerator extends AbstractHeaderGenerator
{
    protected function getSupportedTypes(): array
    {
        return [
            ConfigFileType::TYPE_PLAIN,
        ];
    }

    protected function wrapLine(string $line): string
    {
        return '/* '.$line.' */';
    }
}
