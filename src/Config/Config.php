<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config;

use function array_keys;
use InvalidArgumentException;
use function sprintf;

class Config
{
    /**
     * @var array
     */
    private $definitions;

    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
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
