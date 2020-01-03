<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config\Loader;

use SplFileInfo;
use Syrma\ConfigGenerator\Exception\NotFoundException;
use Syrma\ConfigGenerator\Util\ParameterBag;

class ParameterFileLoader implements ParameterFileLoaderInterface
{
    /**
     * @var ParameterFileLoaderInterface[]
     */
    private $loaders;

    /**
     * @param ParameterFileLoaderInterface[] $loaders
     */
    public function __construct(ParameterFileLoaderInterface ...$loaders)
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

    public function load(SplFileInfo $file): ParameterBag
    {
        return $this->getLoader($file)->load($file);
    }

    private function getLoader(SplFileInfo $file): ParameterFileLoaderInterface
    {
        foreach ($this->loaders as $loader) {
            if ($loader->isSupported($file)) {
                return $loader;
            }
        }

        throw new NotFoundException(sprintf('Not found ParameterFileLoader for file: %s', $file->getBasename()));
    }
}
