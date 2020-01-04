<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config\Factory;

use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Syrma\ConfigGenerator\Config\Builder\DefinitionBuilder;
use Syrma\ConfigGenerator\Config\Builder\EnvironmentDefinitionBuilder;
use Syrma\ConfigGenerator\Config\ConfigDefinition as Def;
use Syrma\ConfigGenerator\Config\ConfigFileType;
use Syrma\ConfigGenerator\Config\Definition;
use Syrma\ConfigGenerator\Config\EnvironmentDefinition;
use Syrma\ConfigGenerator\Config\Loader\ParameterFileAggregateLoader;
use Syrma\ConfigGenerator\Exception\InvalidConfigurationException;
use Syrma\ConfigGenerator\Util\FilesystemToolkit;
use Syrma\ConfigGenerator\Util\ParameterBag;

class DefinitionFactory
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var TemplateNameParserInterface
     */
    private $templateNameParser;

    /**
     * @var ParameterFileAggregateLoader
     */
    private $paramFileLoader;

    /**
     * @var DefinitionBuilderFactory
     */
    private $definitionBuilderFactory;

    private const SCOPE_DEFAULT = 'default';
    private const SCOPE_DEFINITION = 'definition';
    private const SCOPE_ENVIRONMENT = 'environment';

    public function __construct(
        Filesystem $fs,
        TemplateNameParserInterface $templateNameParser,
        ParameterFileAggregateLoader $paramFileLoader,
        DefinitionBuilderFactory $definitionBuilderFactory
    ) {
        $this->fs = $fs;
        $this->templateNameParser = $templateNameParser;
        $this->paramFileLoader = $paramFileLoader;
        $this->definitionBuilderFactory = $definitionBuilderFactory;
    }

    /**
     * @param array $rawConfig - the raw configuration
     *
     * @return Definition[]
     */
    public function createByRawConfig(array $rawConfig): array
    {
        $definitions = [];
        foreach (array_keys($rawConfig[Def::KEY_DEFINITIONS] ?? []) as $id) {
            $defBuilder = $this->definitionBuilderFactory->create($id);
            $this->configureDefinition($rawConfig, $defBuilder);
            $definitions[$id] = $defBuilder->getDefinition();
        }

        return  $definitions;
    }

    private function configureDefinition(array &$rawConfig, DefinitionBuilder $defBuilder): void
    {
        $rawDefConfig = &$rawConfig[Def::KEY_DEFINITIONS][$defBuilder->getId()];
        $defBuilder->setType(ConfigFileType::create($rawDefConfig[Def::KEY_TYPE]));

        foreach (array_keys($rawDefConfig[Def::KEY_ENVIRONMENTS]) as $envId) {
            $envBuilder = $defBuilder->createEnvBuilder($envId);
            $this->configureEnvironment($rawConfig, $envBuilder);
            $defBuilder->addEnv($envBuilder->getEnvironmentDefinition());
        }
    }

    private function configureEnvironment(array &$rawConfig, EnvironmentDefinitionBuilder $envBuilder): void
    {
        $rawDefConfig = &$rawConfig[Def::KEY_DEFINITIONS][$envBuilder->getDefinitionBuilder()->getId()];

        $this->configureTemplate($rawDefConfig, $envBuilder);
        $this->configureOutput($rawDefConfig, $envBuilder);
        $this->configureOutputBasePath($rawConfig, $rawDefConfig, $envBuilder);
        $this->configureParameters($rawConfig, $rawDefConfig, $envBuilder);
    }

    private function configureTemplate(array &$rawDefConfig, EnvironmentDefinitionBuilder $envBuilder): void
    {
        $rawTemplate = $rawDefConfig[Def::KEY_ENVIRONMENTS][$envBuilder->getName()][Def::KEY_TEMPLATE] ?? ($rawDefConfig[Def::KEY_TEMPLATE] ?? null);
        if (empty($rawTemplate)) {
            throw new InvalidConfigurationException(sprintf('The template is not configured for "%s" environment in "%s" definition!', $envBuilder->getName(), $envBuilder->getDefinitionBuilder()->getId()));
        }

        $template = strtr($rawTemplate, $this->createMarkerMap($envBuilder));
        if (false === $this->fs->exists($template)) {
            throw new InvalidConfigurationException(sprintf('The template file "%s" is not exists!', $template));
        }

        $envBuilder->setTemplate($this->templateNameParser->parse($template));
    }

    private function configureOutputBasePath(array &$rawConfig, array &$rawDefConfig, EnvironmentDefinitionBuilder $envBuilder): void
    {
        $basePath = strtr(
            $rawDefConfig[Def::KEY_OUTPUT_BASE_PATH] ?? $rawConfig[Def::KEY_DEFAULTS][Def::KEY_OUTPUT_BASE_PATH],
            $this->createMarkerMap($envBuilder)
        );

        if (false === FilesystemToolkit::isWritableAnyPath($basePath)) {
            throw new InvalidConfigurationException(sprintf('The output path "%s" is not writeable!', $basePath));
        }

        $envBuilder->setOutputPath($basePath);
    }

    private function configureOutput(array &$rawDefConfig, EnvironmentDefinitionBuilder $envBuilder): void
    {
        $rawOutput = $rawDefConfig[Def::KEY_ENVIRONMENTS][$envBuilder->getName()][Def::KEY_OUTPUT] ?? ($rawDefConfig[Def::KEY_OUTPUT] ?? null);

        if (empty($rawOutput)) {
            throw new InvalidConfigurationException(sprintf('The output is not configured for "%s" environment in "%s" definition!', $envBuilder->getName(), $envBuilder->getDefinitionBuilder()->getId()));
        }

        $envBuilder->setOutputFileName(strtr($rawOutput, $this->createMarkerMap($envBuilder)));
    }

    private function configureParameters(array &$rawConfig, array &$rawDefConfig, EnvironmentDefinitionBuilder $envBuilder): void
    {
        $paramFileMap = $this->collectParameterFiles($rawConfig, $rawDefConfig, $envBuilder);

        $paramBag = new ParameterBag([
            EnvironmentDefinition::PARAM_ENV => $envBuilder->getName(),
            EnvironmentDefinition::PARAM_ENVIRONMENT => $envBuilder->getName(),
            EnvironmentDefinition::PARAM_DEFINITION => $envBuilder->getDefinitionBuilder()->getId(),
        ]);

        $paramBag->append($this->paramFileLoader->loadByList(...$paramFileMap[self::SCOPE_DEFAULT]));
        $paramBag->append(new ParameterBag($rawConfig[Def::KEY_DEFAULTS][Def::KEY_PARAMETERS] ?? []));

        $paramBag->append($this->paramFileLoader->loadByList(...$paramFileMap[self::SCOPE_DEFINITION]));
        $paramBag->append(new ParameterBag($rawDefConfig[Def::KEY_PARAMETERS] ?? []));

        $paramBag->append($this->paramFileLoader->loadByList(...$paramFileMap[self::SCOPE_ENVIRONMENT]));
        $paramBag->append(new ParameterBag($rawDefConfig[Def::KEY_ENVIRONMENTS][$envBuilder->getName()][Def::KEY_PARAMETERS] ?? []));

        $envBuilder->setParameters($paramBag);
    }

    private function collectParameterFiles(array &$rawConfig, array &$rawDefConfig, EnvironmentDefinitionBuilder $envBuilder): array
    {
        $paramFileMap = [
            self::SCOPE_DEFAULT => [],
            self::SCOPE_DEFINITION => [],
            self::SCOPE_ENVIRONMENT => [],
        ];

        $markerMap = $this->createMarkerMap($envBuilder);
        foreach ($rawConfig[Def::KEY_DEFINITIONS][Def::KEY_PARAMETER_FILES] ?? [] as $file) {
            $paramFileMap[self::SCOPE_DEFAULT][] = $this->resolveParameterFile($file, $markerMap);
        }

        foreach ($rawDefConfig[Def::KEY_PARAMETER_FILES] ?? [] as $file) {
            $paramFileMap[self::SCOPE_DEFINITION][] = $this->resolveParameterFile($file, $markerMap);
        }

        foreach ($rawDefConfig[Def::KEY_ENVIRONMENTS][$envBuilder->getName()][Def::KEY_PARAMETER_FILES] ?? [] as $file) {
            $paramFileMap[self::SCOPE_ENVIRONMENT][] = $this->resolveParameterFile($file, $markerMap);
        }

        return $paramFileMap;
    }

    private function createMarkerMap(EnvironmentDefinitionBuilder $envBuilder): array
    {
        return [
            Def::MARKER_ENV => $envBuilder->getName(),
            Def::MARKER_ENVIRONMENT => $envBuilder->getName(),
            Def::MARKER_DEFINITION => $envBuilder->getDefinitionBuilder()->getId(),
        ];
    }

    private function resolveParameterFile(string $rawFileName, array $markerMap): SplFileInfo
    {
        $fileName = strtr($rawFileName, $markerMap);

        if (false === $this->fs->exists($fileName)) {
            throw new InvalidConfigurationException(sprintf('The parameter file "%s" is not exists!', $fileName));
        }

        return new SplFileInfo($fileName);
    }
}
