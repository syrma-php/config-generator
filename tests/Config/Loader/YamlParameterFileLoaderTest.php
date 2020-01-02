<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Config\Loader;


use Symfony\Component\Yaml\Parser;
use Syrma\ConfigGenerator\Config\Loader\YamlParameterFileLoader;

class YamlParameterFileLoaderTest extends AbstractParameterFileLoaderTest
{

    private function createLoader(): YamlParameterFileLoader
    {
        return new YamlParameterFileLoader(new Parser());
    }

    public function testEmpty(): void
    {
        $yml = $this->createFileRef(self::FILE_YML_EMPTY);
        $yaml = $this->createFileRef(self::FILE_YAML_EMPTY);

        $this->assertTrue($this->createLoader()->isSupported($yml));
        $this->assertTrue($this->createLoader()->isSupported($yaml));

        $this->assertEquals([], $this->createLoader()->load($yml));
        $this->assertEquals([], $this->createLoader()->load($yaml));
    }

    public function testLoadDataFromRoot(): void
    {
        $yml = $this->createFileRef(self::FILE_YML_ROOT);
        $this->assertEquals([
            'foo' => 'bar',
            'param' => [1,2,3]
        ], $this->createLoader()->load($yml));
    }

    public function testLoadDataFromParameters(): void
    {
        $yml = $this->createFileRef(self::FILE_YML_PARAMS);
        $this->assertEquals([
            'bar' => 'foo',
            'file' => 'param_parameters'
        ], $this->createLoader()->load($yml));
    }
}