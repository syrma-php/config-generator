<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config\Loader;

use SplFileInfo;

interface ParameterFileLoaderInterface
{
    public function isSupported(SplFileInfo $file): bool;

    public function load(SplFileInfo $file): array;
}
