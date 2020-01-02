<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Definition\Factory;

use Syrma\ConfigGenerator\Definition\Definition;
use Syrma\ConfigGenerator\Definition\EnvironmentDefinition;
use function array_replace;
use SplFileInfo;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Syrma\ConfigGenerator\Config\Config;
use Syrma\ConfigGenerator\Config\ConfigDefinition as Def;
use Syrma\ConfigGenerator\Config\Loader\ParameterFileLoaderInterface;

class DefinitionFactory
{
    /**
     * @var TemplateNameParserInterface
     */
    private $templateNameParser;

    /**
     * @var ParameterFileLoaderInterface
     */
    private $paramFileLoader;

    public function __construct(TemplateNameParserInterface $templateNameParser, ParameterFileLoaderInterface $paramFileLoader)
    {
        $this->templateNameParser = $templateNameParser;
        $this->paramFileLoader = $paramFileLoader;
    }

    /**
     * @return Definition[]
     */
    public function createListByConfig(Config $config): array
    {
        $items = [];
        foreach ($config->getDefinitionIds() as $id) {
            $items[$id] = $this->createDefinition($id, $config);
        }

        return  $items;
    }

    private function createDefinition(string $definitionId, Config $config): Definition
    {
        return new Definition(
            $definitionId,
            $config->getDefinition($definitionId)[Def::KEY_TYPE],
            $this->createEnvironments($definitionId, $config)
        );
    }

    /**
     * @return EnvironmentDefinition[]
     */
    private function createEnvironments(string $definitionId, Config $config): array
    {
        $definition = $config->getDefinition($definitionId);

        $items = [];
        foreach ($definition[Def::KEY_ENVIROMENTS] as $envId => $env) {
            $items[$envId] = $this->createEnvironment($definitionId, $envId, $config);
        }

        return $items;
    }

    private function createEnvironment(string $definitionId, string $envId, Config $config): EnvironmentDefinition
    {
        $definition = $config->getDefinition($definitionId);
        $envConfig = $definition[Def::KEY_ENVIROMENTS][$envId];

        $parameters = array_replace(
            [
                'env' => $envId,
                'environment' => $envId,
                'definition' => $definitionId,
            ],
            $this->parseParameterFiles($definition[Def::KEY_PARAMETER_FILES] ?? []),
            $definition[Def::KEY_PARAMETERS],
            $this->parseParameterFiles($envConfig[Def::KEY_PARAMETER_FILES] ?? []),
            $envConfig[Def::KEY_PARAMETERS]
        );

        return new EnvironmentDefinition(
            $envId,
            $this->templateNameParser->parse($envConfig[Def::KEY_TEMPLATE]),
            $envConfig[Def::KEY_OUTPUT_BASE_PATH],
            $envConfig[Def::KEY_OUTPUT],
            $parameters
        );
    }

    /**
     * @param SplFileInfo[] $files
     */
    private function parseParameterFiles(array $files): array
    {
        $configMap = [[]];
        foreach ($files as $file) {
            $configMap[] = $this->paramFileLoader->load($file);
        }

        return array_replace(...$configMap);
    }
}
