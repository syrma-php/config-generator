<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config\Factory;

use Symfony\Component\Filesystem\Filesystem;
use Syrma\ConfigGenerator\Config\ConfigNormalizer;

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

    public function create(array $rawConfig): ConfigNormalizer
    {
        return new ConfigNormalizer($this->fs, $rawConfig);
    }
}
