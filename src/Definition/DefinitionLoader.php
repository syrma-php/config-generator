<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Definition;

use SplFileInfo;
use Syrma\ConfigGenerator\Config\Factory\ConfigFactory;

class DefinitionLoader
{
    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * @var DefinitionFactory
     */
    private $definitionFactory;

    public function __construct(ConfigFactory $configLoader, DefinitionFactory $definitionFactory)
    {
        $this->configFactory = $configLoader;
        $this->definitionFactory = $definitionFactory;
    }

    /**
     * @return Definition[]
     */
    public function load(SplFileInfo $configFile): array
    {
        $config = $this->configFactory->create($configFile);

        return $this->definitionFactory->createListByConfig($config);
    }
}
