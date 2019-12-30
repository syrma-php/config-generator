<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config\Factory;

use SplFileInfo;
use Symfony\Component\Config\Definition\Processor;
use Syrma\ConfigGenerator\Config\Config;
use Syrma\ConfigGenerator\Config\ConfigDefinition;
use Syrma\ConfigGenerator\Config\ConfigDefinition as Def;
use Syrma\ConfigGenerator\Config\Loader\ConfigFileLoaderInterface;

class ConfigFactory
{
    /**
     * @var ConfigFileLoaderInterface
     */
    private $configFileLoader;

    /**
     * @var ConfigNormalizerFactory
     */
    private $normalizerFactory;

    public function __construct(ConfigFileLoaderInterface $configFileLoader, ConfigNormalizerFactory $normalizerFactory)
    {
        $this->configFileLoader = $configFileLoader;
        $this->normalizerFactory = $normalizerFactory;
    }

    public function create(SplFileInfo $configFile): Config
    {
        $rawConfigList = $this->configFileLoader->load($configFile);
        $config = (new Processor())->processConfiguration(new ConfigDefinition(), $rawConfigList);
        $config = $this->normalizerFactory->create($config)->normalize();

        return new Config(
            $config[Def::KEY_DEFINITIONS]
        );
    }
}
