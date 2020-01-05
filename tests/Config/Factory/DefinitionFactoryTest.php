<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Tests\Config\Factory;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReference;
use Syrma\ConfigGenerator\Config\EnvironmentDefinition;
use Syrma\ConfigGenerator\Config\Factory\DefinitionBuilderFactory;
use Syrma\ConfigGenerator\Config\Factory\DefinitionFactory;
use Syrma\ConfigGenerator\Config\Loader\ParameterFileAggregateLoader;
use Syrma\ConfigGenerator\Config\Loader\ParameterFileLoaderInterface;
use Syrma\ConfigGenerator\Exception\InvalidConfigurationException;
use Syrma\ConfigGenerator\Util\ParameterBag;

class DefinitionFactoryTest extends TestCase
{
    public function testEmpty(): void
    {
        $factory = $this->createFactory();
        $this->assertEquals([], $factory->createByRawConfig([]));
    }

    public function testSimple(): void
    {
        $basePath = __DIR__ . '/fixtures/def_factory_simple';

        $result = $this->createFactory()->createByRawConfig(require  $basePath .'/config.php');
        $this->assertCount(2, $result);
        $this->assertEquals(['def0', 'def1'], array_keys($result));

        $def0 = $result['def0'];
        $this->assertSame('def0', $def0->getId());
        $this->assertCount(2, $envMap = $def0->getEnvironmentMap());
        $this->assertEquals(['live', 'dev'], array_keys($envMap));

        $live = $envMap['live'];
        $this->assertEquals('def0', $live->getDefinitionId());
        $this->assertEquals('live', $live->getName());
        $this->assertEquals( $basePath . '/live.def0.tpl' , $live->getTemplate()->getLogicalName());
        $this->assertEquals($basePath, $live->getOutputPath());
        $this->assertEquals('live.def0.conf', $live->getOutputFileName());
        $this->assertEquals($basePath . '/live.def0.conf', $live->getOutputFile());
        $this->assertEquals($this->buildDefaultParameters('def0', 'live'), $live->getParameters()->all());

        $dev = $envMap['dev'];
        $this->assertEquals('def0', $dev->getDefinitionId());
        $this->assertEquals('dev', $dev->getName());
        $this->assertEquals( $basePath . '/dev.def0.tpl' , $dev->getTemplate()->getLogicalName());
        $this->assertEquals($basePath , $dev->getOutputPath());
        $this->assertEquals('dev/dev.def0.conf', $dev->getOutputFileName());
        $this->assertEquals($basePath . '/dev/dev.def0.conf', $dev->getOutputFile());
        $this->assertEquals($this->buildDefaultParameters('def0', 'dev'), $dev->getParameters()->all());


        $def1 = $result['def1'];
        $this->assertSame('def1', $def1->getId());
        $this->assertCount(2, $envMap = $def1->getEnvironmentMap());
        $this->assertEquals(['prod', 'test'], array_keys($envMap));


        $prod = $envMap['prod'];
        $this->assertEquals('def1', $prod->getDefinitionId());
        $this->assertEquals('prod', $prod->getName());
        $this->assertEquals( $basePath . '/prod.def1.tpl' , $prod->getTemplate()->getLogicalName());
        $this->assertEquals($basePath.'/out', $prod->getOutputPath());
        $this->assertEquals('prod.def1.conf', $prod->getOutputFileName());
        $this->assertEquals($basePath . '/out/prod.def1.conf', $prod->getOutputFile());
        $this->assertEquals($this->buildDefaultParameters('def1', 'prod'), $prod->getParameters()->all());

        $test = $envMap['test'];
        $this->assertEquals('def1', $test->getDefinitionId());
        $this->assertEquals('test', $test->getName());
        $this->assertEquals( $basePath . '/fake.tpl' , $test->getTemplate()->getLogicalName());
        $this->assertEquals($basePath .'/out' , $test->getOutputPath());
        $this->assertEquals('conf.d/test.def1.conf', $test->getOutputFileName());
        $this->assertEquals($this->buildDefaultParameters('def1', 'test'), $test->getParameters()->all());
    }

    public function testParam(): void
    {
        $basePath = __DIR__ . '/fixtures/def_factory_param';
        $liveParams = array_replace(
            $this->buildDefaultParameters('def0', 'live'),
            require $basePath . '/expected_live.php'
        );

        $devParams = array_replace(
            $this->buildDefaultParameters('def0', 'dev'),
            require $basePath . '/expected_dev.php'
        );

        $prodParams = array_replace(
            $this->buildDefaultParameters('def1', 'prod'),
            require $basePath . '/expected_prod.php'
        );

        $result = $this->createFactory()->createByRawConfig(require  $basePath .'/config.php');
        $this->assertCount(2, $result);
        $this->assertEquals(['def0', 'def1'], array_keys($result));

        $this->assertCount(2, $envMap = $result['def0']->getEnvironmentMap());
        $this->assertEquals(['live', 'dev'], array_keys($envMap));

        $this->assertEquals($liveParams, $envMap['live']->getParameters()->all());
        $this->assertEquals($devParams, $envMap['dev']->getParameters()->all());

        $this->assertCount(1, $envMap = $result['def1']->getEnvironmentMap());
        $this->assertEquals(['prod'], array_keys($envMap));

        $this->assertEquals($prodParams, $envMap['prod']->getParameters()->all());
    }

    public function testNegativeTemplateEmpty(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(DefinitionFactory::EX_CODE_TEMPLATE_EMPTY);

        $basePath = __DIR__ . '/fixtures/def_factory_negative';
        $this->createFactory()->createByRawConfig(require  $basePath .'/template_empty.php');
    }

    public function testNegativeTemplateIsNotExists(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(DefinitionFactory::EX_CODE_TEMPLATE_IS_NOT_EXISTS);

        $basePath = __DIR__ . '/fixtures/def_factory_negative';
        $this->createFactory()->createByRawConfig(require  $basePath .'/template_is_not_exists.php');
    }

    public function testNegativeParamFileIsNotExists(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(DefinitionFactory::EX_CODE_PARAM_FILE_IS_NOT_EXISTS);

        $basePath = __DIR__ . '/fixtures/def_factory_negative';
        $this->createFactory()->createByRawConfig(require  $basePath .'/param_file_is_not_exists.php');
    }

    public function testNegativeOutputEmpty(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionCode(DefinitionFactory::EX_CODE_OUTPUT_EMPTY);

        $basePath = __DIR__ . '/fixtures/def_factory_negative';
        $this->createFactory()->createByRawConfig(require  $basePath .'/output_empty.php');
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
                return new ParameterBag((array) require $file->getPathname());
            })
        ;

        return new DefinitionFactory(
            new Filesystem(),
            $tplNameParser,
            new ParameterFileAggregateLoader($paramLoader),
            new DefinitionBuilderFactory()
        );
    }

    private function buildDefaultParameters(string $def, string $env) : array
    {
        return [
            EnvironmentDefinition::PARAM_ENV => $env,
            EnvironmentDefinition::PARAM_ENVIRONMENT => $env,
            EnvironmentDefinition::PARAM_DEFINITION => $def,
        ];
    }
}
