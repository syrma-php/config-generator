<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Config\Loader;


use PHPUnit\Framework\TestCase;

abstract class AbstractParameterFileLoaderTest extends TestCase
{

    protected const FILE_YML_EMPTY = 'param_empty.yml';
    protected const FILE_YAML_EMPTY = 'param_empty.yaml';
    protected const FILE_YML_ROOT = 'param_root.yml';
    protected const FILE_YML_PARAMS = 'param_parameters.yml';


    protected function createFileRef( string $name): \SplFileInfo
    {
        return new \SplFileInfo( __DIR__ . '/fixtures/' . $name);
    }
}