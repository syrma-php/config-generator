<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Config\Factory;


use PHPUnit\Framework\TestCase;
use Syrma\ConfigGenerator\Config\ConfigTransformer;
use Syrma\ConfigGenerator\Config\Factory\ConfigFactory;
use Syrma\ConfigGenerator\Config\Factory\ConfigNormalizerFactory;
use Syrma\ConfigGenerator\Config\Loader\ConfigFileLoaderInterface;
use Syrma\ConfigGenerator\Config\ConfigDefinition as Def;
use Syrma\ConfigGenerator\Config\ConfigFileType;

class ConfigFactoryTest extends TestCase
{

    public function testCreate(): void
    {
        $rawConfig = [
            Def::KEY_DEFINITIONS => [
                'def0' => [
                    Def::KEY_TYPE => ConfigFileType::TYPE_YML,
                    Def::KEY_TEMPLATE => 'def0-live.tpl',
                    Def::KEY_OUTPUT_BASE_PATH => 'def0.outputBasePath',
                    Def::KEY_PARAMETERS => ['bar' => 'def0', 'foo' => 'badValue'],
                    Def::KEY_ENVIRONMENTS => [
                        'live' => [
                            Def::KEY_PARAMETERS => ['foo' => 'def0-live'],
                            Def::KEY_OUTPUT => 'def0-live.outputBasePath.conf',
                        ]
                    ]
                ],
                'def1' => [
                    Def::KEY_TYPE => ConfigFileType::TYPE_XML,
                    Def::KEY_TEMPLATE => 'def1-live.tpl',
                    Def::KEY_OUTPUT_BASE_PATH => 'def1.outputBasePath',
                    Def::KEY_ENVIRONMENTS => [
                        'live' => [
                            Def::KEY_OUTPUT => 'def1-live.outputBasePath.conf',
                        ]
                    ]
                ],
            ]
        ];

        $config = $this->createFactory([$rawConfig])->create(new \SplFileInfo(__FILE__));
        $this->assertSame(['def0','def1'], $config->getDefinitionIds());

        $this->assertSame('def0-live.tpl', $config->getDefinition('def0')[Def::KEY_TEMPLATE]);
        $this->assertSame('def1-live.tpl', $config->getDefinition('def1')[Def::KEY_TEMPLATE]);
    }

    private function createFactory(array $configPool): ConfigFactory
    {
        $loader = $this->getMockBuilder(ConfigFileLoaderInterface::class)
            ->getMock();
        $loader->expects($this->once())->method('load')
            ->with($this->isInstanceOf(\SplFileInfo::class))
            ->willReturn($configPool);


        $factory = $this->getMockBuilder(ConfigNormalizerFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())->method('create')->willReturnCallback(function(array $config){

            $normalizer = $this->getMockBuilder(ConfigTransformer::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
            $normalizer->method('transform')->willReturn($config);

            return $normalizer;
        });

        return new ConfigFactory($loader, $factory);
    }

}