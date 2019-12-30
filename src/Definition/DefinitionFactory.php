<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Definition;

use function array_replace;
use SplFileInfo;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Syrma\ConfigGenerator\Config\Config;
use Syrma\ConfigGenerator\Config\ConfigDefinition;
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
            $items[] = $this->createDefinition($id, $config);
        }

        return  $items;
    }

    private function createDefinition(string $definitionId, Config $config): Definition
    {
        return new Definition(
            $definitionId,
            $config->getDefinition($definitionId)[ConfigDefinition::KEY_TYPE],
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
        foreach ($definition[ConfigDefinition::KEY_ENVIROMENTS] as $envId => $env) {
            $items[] = $this->createEnvironment($definitionId, $envId, $config);
        }

        return $items;
    }

    private function createEnvironment(string $definitionId, string $envId, Config $config): EnvironmentDefinition
    {
        $definition = $config->getDefinition($definitionId);
        $envConfig = $definition[ConfigDefinition::KEY_ENVIROMENTS][$envId];

        $parameters = array_replace(
            [
                'env' => $envId,
                'environment' => $envId,
                'definition' => $definition,
            ],
            $this->parseParameterFiles($definition[ConfigDefinition::KEY_PARAMETER_FILES] ?? []),
            $definition[ConfigDefinition::KEY_PARAMETERS],
            $this->parseParameterFiles($envConfig[ConfigDefinition::KEY_PARAMETER_FILES] ?? []),
            $envConfig[ConfigDefinition::KEY_PARAMETERS]
        );

        return new EnvironmentDefinition(
            $envId,
            $this->templateNameParser->parse($envConfig[ConfigDefinition::KEY_TEMPLATE]),
            $envConfig[ConfigDefinition::KEY_OUTPUT_BASE_PATH],
            $envConfig[ConfigDefinition::KEY_OUTPUT],
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
