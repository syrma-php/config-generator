<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Tests\Definition\Factory;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReference;
use Syrma\ConfigGenerator\Config\Factory\DefinitionBuilderFactory;
use Syrma\ConfigGenerator\Config\Factory\DefinitionFactory;
use Syrma\ConfigGenerator\Config\Loader\ParameterFileAggregateLoader;
use Syrma\ConfigGenerator\Config\Loader\ParameterFileLoaderInterface;

class DefinitionFactoryTest extends TestCase
{
    private const PARAM_FILE_0 = __DIR__.'/fixtures/paramFile_0.php';
    private const PARAM_FILE_1 = __DIR__.'/fixtures/paramFile_1.php';

    // @TODO - add more-more tests :D

    public function testEmpty(): void
    {
        $factory = $this->createFactory();
        $this->assertEquals([], $factory->createByRawConfig([]));
    }

    private function createFactory(): DefinitionFactory
    {
        $tplNameParser = $this->getMockBuilder(TemplateNameParserInterface::class)->getMock();
        $tplNameParser->expects($this->any())->method('parse')->willReturnCallback(static function (string $name) {
            return new TemplateReference($name, 'php');
        });

        $paramLoader = $this->getMockBuilder(ParameterFileLoaderInterface::class)->getMock();
        $paramLoader->expects($this->any())->method('load')
            ->willReturnCallback(static function (SplFileInfo $file) {
                return (array) require $file->getPathname();
            })
        ;

        return new DefinitionFactory(
            new Filesystem(),
            $tplNameParser,
            new ParameterFileAggregateLoader($paramLoader),
            new DefinitionBuilderFactory()
        );
    }
}
