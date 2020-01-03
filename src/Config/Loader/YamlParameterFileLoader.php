<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config\Loader;

use Syrma\ConfigGenerator\Util\ParameterBag;
use function in_array;
use SplFileInfo;
use Symfony\Component\Yaml\Parser;

class YamlParameterFileLoader implements ParameterFileLoaderInterface
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

    public function load(SplFileInfo $file): ParameterBag
    {
        $data = $this->parser->parseFile($file->getPathname());

        return new ParameterBag($data['parameters'] ?? (array) $data);
    }
}
