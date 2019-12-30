<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Generator\HeaderGenerator;

use Syrma\ConfigGenerator\Definition\ConfigFileType;

class HashTagBaseHeaderGenerator extends AbstractHeaderGenerator
{
    protected function getSupportedTypes(): array
    {
        return [
            ConfigFileType::TYPE_YML,
            ConfigFileType::TYPE_NGINX,
            ConfigFileType::TYPE_CRON,
        ];
    }

    protected function wrapLine(string $line): string
    {
        return '# '.$line;
    }
}
