<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Definition;

class Definition
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
    private $environmentMap;

    /**
     * @param EnvironmentDefinition[] $environmentMap
     */
    public function __construct(string $id, ConfigFileType $type, array $environmentMap)
    {
        $this->id = $id;
        $this->environmentMap = $environmentMap;
        $this->type = $type;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): ConfigFileType
    {
        return $this->type;
    }

    /**
     * @return EnvironmentDefinition[]
     */
    public function getEnvironmentMap(): array
    {
        return $this->environmentMap;
    }
}
