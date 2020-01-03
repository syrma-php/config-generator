<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Generator;

use Symfony\Component\Console\Style\SymfonyStyle;
use Syrma\ConfigGenerator\Config\Definition;
use Syrma\ConfigGenerator\Config\EnvironmentDefinition;

class GeneratorContext
{
    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var Definition
     */
    private $definition;

    /**
     * @var EnvironmentDefinition
     */
    private $environment;

    public function __construct(SymfonyStyle $io, Definition $definition, EnvironmentDefinition $environment)
    {
        $this->io = $io;
        $this->definition = $definition;
        $this->environment = $environment;
    }

    public function getIo(): SymfonyStyle
    {
        return $this->io;
    }

    public function getDefinition(): Definition
    {
        return $this->definition;
    }

    public function getEnvironment(): EnvironmentDefinition
    {
        return $this->environment;
    }

    public function withDefinition(Definition $definition): self
    {
        $obj = clone $this;
        $obj->definition = $definition;

        return $obj;
    }

    public function withEnvironment(EnvironmentDefinition $environment): self
    {
        $obj = clone $this;
        $obj->environment = $environment;

        return $obj;
    }
}
