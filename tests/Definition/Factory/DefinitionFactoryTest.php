<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Definition\Factory;


use PHPUnit\Framework\TestCase;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReference;
use Syrma\ConfigGenerator\Config\Config;
use Syrma\ConfigGenerator\Config\Loader\ParameterFileLoaderInterface;
use Syrma\ConfigGenerator\Definition\ConfigFileType;
use Syrma\ConfigGenerator\Definition\Definition;
use Syrma\ConfigGenerator\Definition\EnvironmentDefinition;
use Syrma\ConfigGenerator\Definition\Factory\DefinitionFactory;
use Syrma\ConfigGenerator\Config\ConfigDefinition as Def;

class DefinitionFactoryTest extends TestCase
{
    private const PARAM_FILE_0 = __DIR__ . '/fixtures/paramFile_0.php';
    private const PARAM_FILE_1 = __DIR__ . '/fixtures/paramFile_1.php';


    public function testEmpty(): void
    {
        $factory = $this->createFactory();
        $this->assertEquals([], $factory->createListByConfig(new Config([])));
    }

    public function testCreateSingleDefWithMultiEnv(): void
    {
        $config = new Config([
            'def0' => [
                Def::KEY_TYPE => ConfigFileType::create(ConfigFileType::TYPE_YML),
                Def::KEY_PARAMETERS => ['bar' => 'def0', 'foo' => 'badValue'],
                Def::KEY_PARAMETER_FILES => [
                    new \SplFileInfo(self::PARAM_FILE_0)
                ],
                Def::KEY_ENVIROMENTS => [
                    'live' => [
                        Def::KEY_PARAMETERS => ['foo' => 'def0-live'],
                        Def::KEY_TEMPLATE => 'def0-live.tpl',
                        Def::KEY_OUTPUT_BASE_PATH => 'def0-live.outputBasePath',
                        Def::KEY_OUTPUT => 'def0-live.outputBasePath.conf',
                        Def::KEY_PARAMETER_FILES => [
                            new \SplFileInfo(self::PARAM_FILE_1)
                        ],
                    ],
                    'dev' => [
                        Def::KEY_PARAMETERS => ['foo' => 'def0-dev'],
                        Def::KEY_TEMPLATE => 'def0-dev.tpl',
                        Def::KEY_OUTPUT_BASE_PATH => 'def0-dev.outputBasePath',
                        Def::KEY_OUTPUT => 'def0-dev.outputBasePath.conf',
                    ]
                ]
            ]
        ]);

        $result = $this->createFactory()->createListByConfig($config);
        $this->assertCount(1, $result);

        $def = array_shift($result);
        $this->assertInstanceOf(Definition::class, $def);

        $this->assertSame('def0', $def->getId());
        $this->assertSame(ConfigFileType::TYPE_YML, $def->getType()->getValue());

        $envList = $def->getEnvironmentMap();
        $this->assertCount(2, $envList);
        $this->assertArrayHasKey('live', $envList);
        $this->assertArrayHasKey('dev', $envList);

        $envLive = $envList['live'];

        /** @var EnvironmentDefinition $envLive */
        $this->assertSame('live', $envLive->getName());
        $this->assertSame('def0-live.tpl', $envLive->getTemplate()->getLogicalName());
        $this->assertSame('def0-live.outputBasePath/def0-live.outputBasePath.conf', $envLive->getOutputFile());
        $this->assertSame('def0-live.outputBasePath', $envLive->getOutputPath());
        $this->assertSame('def0-live.outputBasePath.conf', $envLive->getOutputFileName());
        $this->assertSame([
            'env' => 'live',
            'environment' => 'live',
            'definition' => 'def0',
            'paramFile_0' => 0,
            'fooFile' => 'f1',
            'bar' => 'def0',
            'foo' => 'def0-live',
            'paramFile_1' => 1,
        ], $envLive->getParameters());

        $envDev = $envList['dev'];

        /** @var EnvironmentDefinition $envDev */
        $this->assertSame('dev', $envDev->getName());
        $this->assertSame('def0-dev.tpl', $envDev->getTemplate()->getLogicalName());
        $this->assertSame('def0-dev.outputBasePath/def0-dev.outputBasePath.conf', $envDev->getOutputFile());
        $this->assertSame('def0-dev.outputBasePath', $envDev->getOutputPath());
        $this->assertSame('def0-dev.outputBasePath.conf', $envDev->getOutputFileName());
        $this->assertSame([
            'env' => 'dev',
            'environment' => 'dev',
            'definition' => 'def0',
            'paramFile_0' => 0,
            'fooFile' => 'f0',
            'bar' => 'def0',
            'foo' => 'def0-dev',
        ], $envDev->getParameters());
    }
    public function testCreateMultiDefWithSingleEnv(): void
    {
        $config = new Config([
            'def0' => [
                Def::KEY_TYPE => ConfigFileType::create(ConfigFileType::TYPE_YML),
                Def::KEY_PARAMETERS => ['bar' => 'def0', 'foo' => 'badValue'],
                Def::KEY_PARAMETER_FILES => [
                    new \SplFileInfo(self::PARAM_FILE_0)
                ],
                Def::KEY_ENVIROMENTS => [
                    'live' => [
                        Def::KEY_PARAMETERS => ['foo' => 'def0-live'],
                        Def::KEY_TEMPLATE => 'def0-live.tpl',
                        Def::KEY_OUTPUT_BASE_PATH => 'def0-live.outputBasePath',
                        Def::KEY_OUTPUT => 'def0-live.outputBasePath.conf',
                        Def::KEY_PARAMETER_FILES => [
                            new \SplFileInfo(self::PARAM_FILE_1)
                        ],
                    ]
                ]
            ],
            'def1' => [
                Def::KEY_TYPE => ConfigFileType::create(ConfigFileType::TYPE_YML),
                Def::KEY_PARAMETERS => ['bar' => 'def1', 'foo' => 'badValue'],
                Def::KEY_ENVIROMENTS => [
                    'dev' => [
                        Def::KEY_PARAMETERS => ['foo' => 'def1-dev'],
                        Def::KEY_TEMPLATE => 'def1-dev.tpl',
                        Def::KEY_OUTPUT_BASE_PATH => 'def1-dev.outputBasePath',
                        Def::KEY_OUTPUT => 'def1-dev.outputBasePath.conf',
                    ]
                ]
            ]
        ]);

        $result = $this->createFactory()->createListByConfig($config);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('def0', $result);
        $this->assertArrayHasKey('def1', $result);

        $def0 = $result['def0'];
        $this->assertInstanceOf(Definition::class, $def0);

        $this->assertSame('def0', $def0->getId());
        $this->assertSame(ConfigFileType::TYPE_YML, $def0->getType()->getValue());

        $envList = $def0->getEnvironmentMap();
        $this->assertCount(1, $envList);
        $this->assertArrayHasKey('live', $envList);

        $envLive = $envList['live'];

        /** @var EnvironmentDefinition $envLive */
        $this->assertSame('live', $envLive->getName());
        $this->assertSame('def0-live.tpl', $envLive->getTemplate()->getLogicalName());
        $this->assertSame('def0-live.outputBasePath/def0-live.outputBasePath.conf', $envLive->getOutputFile());
        $this->assertSame('def0-live.outputBasePath', $envLive->getOutputPath());
        $this->assertSame('def0-live.outputBasePath.conf', $envLive->getOutputFileName());
        $this->assertSame([
            'env' => 'live',
            'environment' => 'live',
            'definition' => 'def0',
            'paramFile_0' => 0,
            'fooFile' => 'f1',
            'bar' => 'def0',
            'foo' => 'def0-live',
            'paramFile_1' => 1,
        ], $envLive->getParameters());


        $dev1 = $result['def1'];
        $this->assertInstanceOf(Definition::class, $dev1);

        $this->assertSame('def1', $dev1->getId());
        $this->assertSame(ConfigFileType::TYPE_YML, $dev1->getType()->getValue());

        $envList = $dev1->getEnvironmentMap();
        $this->assertCount(1, $envList);
        $this->assertArrayHasKey('dev', $envList);

        $envDev = $envList['dev'];

        /** @var EnvironmentDefinition $envDev */
        $this->assertSame('dev', $envDev->getName());
        $this->assertSame('def1-dev.tpl', $envDev->getTemplate()->getLogicalName());
        $this->assertSame('def1-dev.outputBasePath/def1-dev.outputBasePath.conf', $envDev->getOutputFile());
        $this->assertSame('def1-dev.outputBasePath', $envDev->getOutputPath());
        $this->assertSame('def1-dev.outputBasePath.conf', $envDev->getOutputFileName());
        $this->assertSame([
            'env' => 'dev',
            'environment' => 'dev',
            'definition' => 'def1',
            'bar' => 'def1',
            'foo' => 'def1-dev',
        ], $envDev->getParameters());
    }



    private function createFactory(): DefinitionFactory
    {
        $tplNameParser = $this->getMockBuilder(TemplateNameParserInterface::class)->getMock();
        $tplNameParser->expects($this->any())->method('parse')->willReturnCallback(static function(string $name){
            return new TemplateReference($name, 'php');
        });

        $paramLoader = $this->getMockBuilder(ParameterFileLoaderInterface::class)->getMock();
        $paramLoader->expects($this->any())->method('load')
            ->willReturnCallback(static function (\SplFileInfo $file ){
                return (array)require $file->getPathname();
            })
        ;

        return new DefinitionFactory($tplNameParser, $paramLoader);
    }
}