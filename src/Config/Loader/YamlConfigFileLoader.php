<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config\Loader;

use const DIRECTORY_SEPARATOR;
use function in_array;
use SplFileInfo;
use Symfony\Component\Yaml\Parser;
use Syrma\ConfigGenerator\Config\ConfigDefinition as Def;
use Webmozart\PathUtil\Path;

class YamlConfigFileLoader implements ConfigFileLoaderInterface
{
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function isSupported(SplFileInfo $file): bool
    {
        return in_array($file->getExtension(), ['yml', 'yaml'], true);
    }

    public function load(SplFileInfo $file): array
    {
        $configList = [];
        $this->doLoad($configList, $file);

        return $configList;
    }

    private function doLoad(array &$configList, SplFileInfo $file): void
    {
        if (isset($configList[$file->getPathname()])) {
            return; // it was loaded
        }

        $config = (array) $this->parser->parseFile($file->getPathname());
        $this->resolvePath($config, $file->getPath());

        $importPool = [];
        if (!empty($config['imports'])) {
            foreach ((array) $config['imports'] as $import) {
                if (!empty($import['resource'])) {
                    $importPool[] = $this->resolvePathValue($import['resource'], $file->getPath());
                }
            }
            unset($config['imports']);
        }

        $configList[$file->getPathname()] = $config;

        foreach ($importPool as $importFile) {
            $this->doLoad($configList, new SplFileInfo($importFile));
        }
    }

    private function resolvePath(array &$config, string $rootPath): void
    {
        if (!empty($config[Def::KEY_DEFAULTS][Def::KEY_OUTPUT_BASE_PATH])) {
            $config[Def::KEY_DEFAULTS][Def::KEY_OUTPUT_BASE_PATH] = $this->resolvePathValue($config[Def::KEY_DEFAULTS][Def::KEY_OUTPUT_BASE_PATH], $rootPath);
        }

        if (!empty($config[Def::KEY_DEFAULTS][Def::KEY_PARAMETER_FILES])) {
            $config[Def::KEY_DEFAULTS][Def::KEY_PARAMETER_FILES] = $this->resolvePathValueList((array) $config[Def::KEY_DEFAULTS][Def::KEY_PARAMETER_FILES], $rootPath);
        }

        if (!empty($config[Def::KEY_DEFINITIONS])) {
            foreach ((array) $config[Def::KEY_DEFINITIONS] as $defId => $defConf) {
                foreach ([Def::KEY_OUTPUT_BASE_PATH, Def::KEY_TEMPLATE] as $key) {
                    if (!empty($defConf[$key])) {
                        $config[Def::KEY_DEFINITIONS][$defId][$key] = $this->resolvePathValue($defConf[$key], $rootPath);
                    }
                }

                foreach ([Def::KEY_PARAMETER_FILES] as $key) {
                    if (!empty($defConf[$key])) {
                        $config[Def::KEY_DEFINITIONS][$defId][$key] = $this->resolvePathValueList((array) $defConf[$key], $rootPath);
                    }
                }

                if (!empty($defConf[Def::KEY_ENVIRONMENTS])) {
                    foreach ((array) $defConf[Def::KEY_ENVIRONMENTS] as $envId => $envConf) {
                        if (!empty($envConf[Def::KEY_PARAMETER_FILES])) {
                            $config[Def::KEY_DEFINITIONS][$defId][Def::KEY_ENVIRONMENTS][$envId][Def::KEY_PARAMETER_FILES] = $this->resolvePathValueList((array) $envConf[Def::KEY_PARAMETER_FILES], $rootPath);
                        }

                        if (!empty($envConf[Def::KEY_TEMPLATE])) {
                            $config[Def::KEY_DEFINITIONS][$defId][Def::KEY_ENVIRONMENTS][$envId][Def::KEY_TEMPLATE] = $this->resolvePathValue($envConf[Def::KEY_TEMPLATE], $rootPath);
                        }
                    }
                }
            }
        }
    }

    private function resolvePathValue(string $path, string $rootPath): string
    {
        return Path::canonicalize(Path::isAbsolute($path) ? $path : $rootPath.DIRECTORY_SEPARATOR.$path);
    }

    private function resolvePathValueList(array $pathList, string $rootPath): array
    {
        $result = [];
        foreach ($pathList as $i => $path) {
            $result[$i] = $this->resolvePathValue($path, $rootPath);
        }

        return $result;
    }
}
