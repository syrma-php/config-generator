<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config\Builder;

use Syrma\ConfigGenerator\Config\ConfigFileType;
use Syrma\ConfigGenerator\Config\Definition;
use Syrma\ConfigGenerator\Config\EnvironmentDefinition;
use Webmozart\Assert\Assert;

class DefinitionBuilder
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var ConfigFileType
     */
    private $type;

    /**
     * @var EnvironmentDefinition[]
     */
    private $environmentMap = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setType(ConfigFileType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function addEnv(EnvironmentDefinition $environmentDefinition): self
    {
        Assert::eq($environmentDefinition->getDefinitionId(), $this->id);
        $this->environmentMap[$environmentDefinition->getName()] = $environmentDefinition;

        return $this;
    }

    public function createEnvBuilder(string $envName): EnvironmentDefinitionBuilder
    {
        return new EnvironmentDefinitionBuilder($this, $envName);
    }

    public function getDefinition(): Definition
    {
        Assert::isNonEmptyMap($this->environmentMap);
        Assert::notNull($this->type);

        return new Definition($this->id, $this->type, $this->environmentMap);
    }
}
