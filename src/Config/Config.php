<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config;

use function array_keys;
use InvalidArgumentException;
use function sprintf;
use Webmozart\Assert\Assert;

class Config
{
    /**
     * @var array
     */
    private $definitions = [];

    /**
     * @param Definition[] $definitions
     */
    public function __construct(array $definitions)
    {
        foreach ($definitions as $definition) {
            Assert::isInstanceOf($definition, Definition::class);
            $this->definitions[$definition->getId()] = $definition;
        }
    }

    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * @return string[]
     */
    public function getDefinitionIds(): array
    {
        return array_keys($this->definitions);
    }

    public function getDefinition(string $id): Definition
    {
        if (isset($this->definitions[$id])) {
            return $this->definitions[$id];
        }

        throw new InvalidArgumentException(sprintf('The "%s" not exists', $id));
    }
}
