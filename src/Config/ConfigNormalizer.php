<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config;

use function dirname;
use SplFileInfo;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;
use Syrma\ConfigGenerator\Config\ConfigDefinition as Def;
use Syrma\ConfigGenerator\Definition\ConfigFileType;
use Syrma\ConfigGenerator\Exception\InvalidConfigurationException;

class ConfigNormalizer
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var array
     */
    private $defaults;

    /**
     * @var array
     */
    private $definitions;

    public function __construct(Filesystem $fs, array $rawConfig)
    {
        $this->fs = $fs;
        $this->defaults = array_replace([Def::KEY_TEMPLATE_SEARCH_PATHS => [], Def::KEY_OUTPUT_BASE_PATH => []], $rawConfig[Def::KEY_DEFAULTS] ?? []);
        $this->definitions = $rawConfig[Def::KEY_DEFINITIONS] ?? [];
    }

    public function normalize(): array
    {
        foreach (array_keys($this->definitions) as $id) {
            $this->normalizeDefinition($id);
        }

        return  [
            Def::KEY_DEFINITIONS => $this->definitions,
        ];
    }

    private function normalizeDefinition(string $definitionId): void
    {
        $this->definitions[$definitionId][Def::KEY_TYPE] = ConfigFileType::create($this->definitions[$definitionId][Def::KEY_TYPE]);
        $this->definitions[$definitionId][Def::KEY_PARAMETER_FILES] = $this->normalizeParameterFiles(
            $this->definitions[$definitionId][Def::KEY_PARAMETER_FILES] ?? [],
            $definitionId,
            null
        );

        foreach (array_keys($this->definitions[$definitionId][Def::KEY_ENVIROMENTS]) as $envId) {
            $this->normalizeEnvironment($definitionId, $envId);
        }
    }

    private function normalizeEnvironment(string $definitionId, string $envId): void
    {
        $envConfig = &$this->definitions[$definitionId][Def::KEY_ENVIROMENTS][$envId];

        $envConfig[Def::KEY_TEMPLATE] = $this->normalizeTemplate($definitionId, $envId);
        $envConfig[Def::KEY_OUTPUT_BASE_PATH] = $this->normalizeOutputBasePath($definitionId, $envId);
        $envConfig[Def::KEY_OUTPUT] = $this->normalizeOutput($definitionId, $envId);
        $envConfig[Def::KEY_PARAMETER_FILES] = $this->normalizeParameterFiles($envConfig[Def::KEY_PARAMETER_FILES] ?? [], $definitionId, $envId);
    }

    private function normalizeTemplate(string $definitionId, string $envId): string
    {
        $markerMap = $this->createMarkerMap($definitionId, $envId);

        $templateName = strtr($this->definitions[$definitionId][Def::KEY_TEMPLATE], $markerMap);

        $rawSearchPathList = !empty($this->definitions[$definitionId][Def::KEY_TEMPLATE_SEARCH_PATHS]) ? $this->definitions[$definitionId][Def::KEY_TEMPLATE_SEARCH_PATHS] : $this->defaults[Def::KEY_TEMPLATE_SEARCH_PATHS];
        $searchList = [];

        foreach ($rawSearchPathList as $searchPath) {
            $searchList[] = strtr($searchPath, $markerMap);
        }

        return (new FileLocator($searchList))->locate($templateName);
    }

    private function normalizeOutputBasePath(string $definitionId, string $envId): string
    {
        $basePath = strtr(
            $this->definitions[$definitionId][Def::KEY_OUTPUT_BASE_PATH] ?? $this->defaults[Def::KEY_OUTPUT_BASE_PATH],
            $this->createMarkerMap($definitionId, $envId)
        );

        $prevPath = null;
        $currPath = $basePath;
        while ($prevPath !== $currPath) {
            if (false !== $real = realpath($currPath)) {
                $currPath = $real;
            }

            if (true === is_dir($currPath) && true === is_writable($currPath)) {
                return $basePath;
            }

            $prevPath = $currPath;
            $currPath = dirname($currPath);
        }

        throw new InvalidConfigurationException(sprintf('The output path "%s" is not writeable!', $basePath));
    }

    private function normalizeOutput(string $definitionId, string $envId): string
    {
        return strtr(
            $this->definitions[$definitionId][Def::KEY_ENVIROMENTS][$envId][Def::KEY_OUTPUT],
            $this->createMarkerMap($definitionId, $envId)
        );
    }

    private function createMarkerMap(string $definitionId, ?string $envId): array
    {
        $map = [
            Def::MARKER_DEFINITION => $definitionId,
        ];

        if (null !== $envId) {
            $map[Def::MARKER_ENV] = $map[Def::MARKER_ENVIROMENT] = $envId;
        }

        return $map;
    }

    /**
     * @param string[] $fileNames
     *
     * @return SplFileInfo[]
     */
    private function normalizeParameterFiles(array $fileNames, string $definitionId, ?string $envId): array
    {
        $fileList = [];

        foreach ($fileNames as $rawFileName) {
            $fileName = strtr($rawFileName, $this->createMarkerMap($definitionId, $envId));

            if ($this->fs->exists($fileName)) {
                $fileList[] = new SplFileInfo($fileName);
            } else {
                throw new InvalidConfigurationException(sprintf('The parameter file "%s" is not exists!', $fileName));
            }
        }

        return $fileList;
    }
}
