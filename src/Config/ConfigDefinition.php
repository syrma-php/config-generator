<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config;

use const DIRECTORY_SEPARATOR;
use function getcwd;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Syrma\ConfigGenerator\Definition\ConfigFileType;

class ConfigDefinition implements ConfigurationInterface
{
    public const KEY_DEFAULTS = 'defaults';
    public const KEY_OUTPUT_BASE_PATH = 'outputBasePath';
    public const KEY_TEMPLATE_SEARCH_PATHS = 'templateSearchPaths';
    public const KEY_DEFINITIONS = 'definitions';
    public const KEY_TEMPLATE = 'template';
    public const KEY_ENVIROMENTS = 'enviroments';
    public const KEY_OUTPUT = 'output';
    public const KEY_PARAMETERS = 'parameters';
    public const KEY_PARAMETER_FILES = 'parameterFiles';
    public const KEY_TYPE = 'type';
    public const MARKER_ENV = '{{env}}';
    public const MARKER_ENVIROMENT = '{{enviroment}}';
    public const MARKER_DEFINITION = '{{definition}}';

    private const MSG_PLACEHOLDERS = 'Available placeholders: '.self::MARKER_ENV.', '.self::MARKER_ENVIROMENT.', '.self::MARKER_DEFINITION.'.';
    private const MSG_PLACEHOLDERS_SHORT = 'Available placeholders: '.self::MARKER_DEFINITION.'.';
    private const MSG_PATH = 'Absolute path or relative for this file.';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('config-generator');

        $this->addDefaultSection($treeBuilder->getRootNode());
        $this->addDefinitionSection($treeBuilder->getRootNode());

        return $treeBuilder;
    }

    // @TODO - Relative hivatkozásokat kidobni
    // @TODO - paraméter hivatkozásokat kiszervzni

    private function addDefaultSection(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode(self::KEY_DEFAULTS)
                    ->children()

                        ->scalarNode(self::KEY_OUTPUT_BASE_PATH)
                            ->info('Default output path for generator. '.self::MSG_PATH.PHP_EOL.self::MSG_PLACEHOLDERS)
                            ->defaultValue(getcwd())
                            ->cannotBeEmpty()
                        ->end()

                        ->arrayNode(self::KEY_TEMPLATE_SEARCH_PATHS)
                            ->info('Search list for template searching. '.self::MSG_PATH.PHP_EOL.self::MSG_PLACEHOLDERS)
                            ->defaultValue([getcwd().DIRECTORY_SEPARATOR.'templates'])
                            ->prototype('scalar')->end()
                        ->end()

                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addDefinitionSection(ArrayNodeDefinition $root): void
    {
        $root
            ->children()
                ->arrayNode(self::KEY_DEFINITIONS)
                ->useAttributeAsKey('definitionId')
                ->prototype('array')
                    ->children()

                        ->scalarNode(self::KEY_TEMPLATE)
                            ->info('Template for current definition. '.self::MSG_PATH.PHP_EOL.self::MSG_PLACEHOLDERS)
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()

                        ->enumNode(self::KEY_TYPE)
                            ->info('Type of the configuration file')
                            ->isRequired()
                            ->values(ConfigFileType::ALL)
                        ->end()

                        ->scalarNode(self::KEY_OUTPUT_BASE_PATH)
                            ->info('Output base path for generation. '.self::MSG_PATH.PHP_EOL.'If it is empty then it use default.outputBasePath.'.PHP_EOL.self::MSG_PLACEHOLDERS)
                        ->end()

                        ->arrayNode(self::KEY_TEMPLATE_SEARCH_PATHS)
                            ->info('Search list for template searching.'.self::MSG_PATH.PHP_EOL.'If it is empty then it use default.templateSearchPaths.'.PHP_EOL.self::MSG_PLACEHOLDERS)
                            ->prototype('scalar')->end()
                        ->end()

                        ->arrayNode(self::KEY_PARAMETERS)
                            ->info('Environment independent parameters for this definition.')
                            ->prototype('variable')->end()
                        ->end()

                        ->arrayNode(self::KEY_PARAMETER_FILES)
                            ->info('List of extra parameter files definition scope. '.self::MSG_PATH.PHP_EOL.self::MSG_PLACEHOLDERS_SHORT)
                            ->prototype('scalar')->end()
                        ->end()

                        ->arrayNode(self::KEY_ENVIROMENTS)
                            ->info('List of enviroments')
                            ->useAttributeAsKey('envId')
                            ->requiresAtLeastOneElement()
                            ->prototype('array')
                                ->children()

                                    ->scalarNode(self::KEY_OUTPUT)
                                        ->info('Output file name. Absolute file name or relative for '.self::KEY_OUTPUT_BASE_PATH.PHP_EOL.self::MSG_PLACEHOLDERS)
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()

                                    ->arrayNode(self::KEY_PARAMETERS)
                                        ->info('Environment dependent parameters for this env.')
                                        ->prototype('variable')->end()
                                    ->end()

                                    ->arrayNode(self::KEY_PARAMETER_FILES)
                                        ->info('List of extra parameters for this env. '.self::MSG_PATH.PHP_EOL.self::MSG_PLACEHOLDERS)
                                        ->prototype('scalar')->end()
                                    ->end()

                                ->end()
                            ->end()
                        ->end()

                    ->end()
                ->end()
            ->end()
        ;
    }
}
