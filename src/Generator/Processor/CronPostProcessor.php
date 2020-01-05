<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Generator\Processor;

use Syrma\ConfigGenerator\Config\ConfigFileType;
use Syrma\ConfigGenerator\Generator\GeneratorContext;

class CronPostProcessor implements PostProcessorInterface
{
    public function isSupported(GeneratorContext $context): bool
    {
        return ConfigFileType::TYPE_CRON === $context->getDefinition()->getType()->getValue();
    }

    public function process(string $content, GeneratorContext $context): string
    {
        return $content.PHP_EOL;
    }
}
