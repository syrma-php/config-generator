<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config\Factory;

use Symfony\Component\Filesystem\Filesystem;
use Syrma\ConfigGenerator\Config\ConfigTransformer;

class ConfigNormalizerFactory
{
    /**
     * @var Filesystem
     */
    private $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    public function create(array $rawConfig): ConfigTransformer
    {
        return new ConfigTransformer($this->fs, $rawConfig);
    }
}
