<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config;

use function getcwd;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigDefinition implements ConfigurationInterface
{
    public const KEY_DEFAULTS = 'defaults';
    public const KEY_OUTPUT_BASE_PATH = 'outputBasePath';
    public const KEY_DEFINITIONS = 'definitions';
    public const KEY_TEMPLATE = 'template';
    public const KEY_ENVIRONMENTS = 'environments';
    public const KEY_OUTPUT = 'output';
    public const KEY_PARAMETERS = 'parameters';
    public const KEY_PARAMETER_FILES = 'parameterFiles';
    public const KEY_TYPE = 'type';
    public const MARKER_ENV = '{{env}}';
    public const MARKER_ENVIRONMENT = '{{environment}}';
    public const MARKER_DEFINITION = '{{definition}}';

    private const MSG_PLACEHOLDERS = 'Available placeholders in value: '.self::MARKER_ENV.', '.self::MARKER_ENVIRONMENT.', '.self::MARKER_DEFINITION.'.';
    private const MSG_PATH = 'Absolute path or relative for this file.';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('config-generator');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // @codeCoverageIgnoreStart
            // Symfony 3.* fallback
            $rootNode = $treeBuilder->root('config-generator');
            // @codeCoverageIgnoreEnd
        }

        $this->addDefaultSection($rootNode);
        $this->addDefinitionSection($rootNode);

        return $treeBuilder;
    }

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

                        ->arrayNode(self::KEY_PARAMETERS)
                            ->info('List of parameters for all definition envs.')
                            ->prototype('variable')->end()
                        ->end()

                        ->arrayNode(self::KEY_PARAMETER_FILES)
                            ->info('List of extra parameter files for all definition scopes. '.self::MSG_PATH.PHP_EOL.self::MSG_PLACEHOLDERS)
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

                        ->arrayNode(self::KEY_PARAMETERS)
                            ->info('Environment independent parameters for this definition.')
                            ->prototype('variable')->end()
                        ->end()

                        ->arrayNode(self::KEY_PARAMETER_FILES)
                            ->info('List of extra parameter files definition scope. '.self::MSG_PATH.PHP_EOL.self::MSG_PLACEHOLDERS)
                            ->prototype('scalar')->end()
                        ->end()

                        ->arrayNode(self::KEY_ENVIRONMENTS)
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
                                        ->info('Environment dependent parameters for this env.' . PHP_EOL . 'The $env, $environment and $definition variables automatic add this config')
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
