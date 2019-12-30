<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config\Loader;

use SplFileInfo;
use Syrma\ConfigGenerator\Exception\NotFoundException;

class ConfigFileLoader implements ConfigFileLoaderInterface
{
    /**
     * @var ConfigFileLoaderInterface[]
     */
    private $loaders;

    /**
     * @param ConfigFileLoaderInterface[] $loaders
     */
    public function __construct(ConfigFileLoaderInterface ...$loaders)
    {
        $this->loaders = $loaders;
    }

    public function isSupported(SplFileInfo $file): bool
    {
        try {
            $this->getLoader($file);

            return true;
        } catch (NotFoundException $ex) {
            return false;
        }
    }

    public function load(SplFileInfo $file): array
    {
        return $this->getLoader($file)->load($file);
    }

    private function getLoader(SplFileInfo $file): ConfigFileLoaderInterface
    {
        foreach ($this->loaders as $loader) {
            if ($loader->isSupported($file)) {
                return $loader;
            }
        }

        throw new NotFoundException(sprintf('Not found ConfigFileLoader for file: %s', $file->getBasename()));
    }
}
