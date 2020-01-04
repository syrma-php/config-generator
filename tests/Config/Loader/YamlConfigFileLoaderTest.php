<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Tests\Config\Loader;

use SplFileInfo;
use Symfony\Component\Yaml\Parser;
use Syrma\ConfigGenerator\Config\ConfigDefinition as Def;
use Syrma\ConfigGenerator\Config\ConfigFileType;
use Syrma\ConfigGenerator\Config\Loader\YamlConfigFileLoader;

class YamlConfigFileLoaderTest extends AbstractConfigFileLoaderTest
{
    private const PATH_FIXTURES = __DIR__.'/fixtures';
    private const FILE_EMPTY = 'conf_empty.yml';
    private const FILE_SIMPLE = 'conf_simple.yml';

    public function testIsSupported(): void
    {
        $loader = $this->createLoader();
        $this->assertTrue($loader->isSupported($this->createFileRef(self::FILE_EMPTY)));
        $this->assertFalse($loader->isSupported(new SplFileInfo(__FILE__)));
    }

    public function testSimple(): void
    {
        $def0 = [
            Def::KEY_DEFINITIONS => [
                'def0' => [
                    Def::KEY_TYPE => ConfigFileType::TYPE_YML,
                    Def::KEY_TEMPLATE => self::PATH_FIXTURES.'/templates/foo.tpl',
                    Def::KEY_OUTPUT_BASE_PATH => self::PATH_FIXTURES.'/out',
                    Def::KEY_ENVIRONMENTS => [
                        'live' => [
                            Def::KEY_OUTPUT => 'live.conf',
                        ],
                    ],
                ],
            ],
        ];

        $resultList = $this->createLoader()->load($this->createFileRef(self::FILE_SIMPLE));
        $this->assertSame([self::PATH_FIXTURES.'/'.self::FILE_SIMPLE => $def0], $resultList);
    }

    private function createLoader(): YamlConfigFileLoader
    {
        return new YamlConfigFileLoader(new Parser());
    }
}
