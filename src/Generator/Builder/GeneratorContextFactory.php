<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Generator\Builder;

use Symfony\Component\Console\Style\SymfonyStyle;
use Syrma\ConfigGenerator\Definition\Definition;
use Syrma\ConfigGenerator\Definition\EnvironmentDefinition;
use Syrma\ConfigGenerator\Generator\GeneratorContext;

class GeneratorContextFactory
{
    public function createContext(SymfonyStyle $io, Definition $definition, EnvironmentDefinition $environment): GeneratorContext
    {
        return new GeneratorContext($io, $definition, $environment);
    }
}
