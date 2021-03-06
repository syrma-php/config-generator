<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config\Factory;

use SplFileInfo;
use Symfony\Component\Config\Definition\Processor;
use Syrma\ConfigGenerator\Config\Config;
use Syrma\ConfigGenerator\Config\ConfigDefinition;
use Syrma\ConfigGenerator\Config\Loader\ConfigFileLoaderInterface;

class ConfigFactory
{
    /**
     * @var ConfigFileLoaderInterface
     */
    private $configFileLoader;

    /**
     * @var DefinitionFactory
     */
    private $definitionFactory;

    public function __construct(ConfigFileLoaderInterface $configFileLoader, DefinitionFactory $definitionFactory)
    {
        $this->configFileLoader = $configFileLoader;
        $this->definitionFactory = $definitionFactory;
    }

    public function create(SplFileInfo $configFile): Config
    {
        $rawConfig = $this->loadRawConfig($configFile);

        return new Config(
            $this->definitionFactory->createByRawConfig($rawConfig)
        );
    }

    private function loadRawConfig(SplFileInfo $configFile): array
    {
        return (new Processor())->processConfiguration(
            new ConfigDefinition(),
            $this->configFileLoader->load($configFile)
        );
    }
}
