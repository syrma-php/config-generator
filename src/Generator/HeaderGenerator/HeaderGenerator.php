<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Generator\HeaderGenerator;

use Syrma\ConfigGenerator\Exception\NotFoundException;
use Syrma\ConfigGenerator\Generator\GeneratorContext;

class HeaderGenerator implements HeaderGeneratorInterface
{
    /**
     * @var HeaderGeneratorInterface[]
     */
    private $generators;

    /**
     * @param HeaderGeneratorInterface[] $generators
     */
    public function __construct(HeaderGeneratorInterface ...$generators)
    {
        $this->generators = $generators;
    }

    public function isSupported(GeneratorContext $context): bool
    {
        try {
            $this->getGenerator($context);

            return true;
        } catch (NotFoundException $ex) {
            return false;
        }
    }

    public function generateHeader(string $configContent, GeneratorContext $context): string
    {
        return $this->getGenerator($context)->generateHeader($configContent, $context);
    }

    public function isModified(string $oldConfigFileContent, GeneratorContext $context): bool
    {
        return $this->getGenerator($context)->isModified($oldConfigFileContent, $context);
    }

    private function getGenerator(GeneratorContext $context): HeaderGeneratorInterface
    {
        foreach ($this->generators as $generator) {
            if (true === $generator->isSupported($context)) {
                return $generator;
            }
        }

        $definition = $context->getDefinition();
        throw new NotFoundException(sprintf('Not found HeaderGenerator for definition: %s (type: %s)', $definition->getId(), $definition->getType()->getValue()));
    }
}
