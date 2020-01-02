<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Definition\Loader;


use PHPUnit\Framework\TestCase;
use Syrma\ConfigGenerator\Config\Config;
use Syrma\ConfigGenerator\Config\Factory\ConfigFactory;
use Syrma\ConfigGenerator\Definition\Factory\DefinitionFactory;
use Syrma\ConfigGenerator\Definition\Loader\DefinitionLoader;

class DefinitionLoaderTest extends TestCase
{

    public function testLoad(): void
    {
        $configFactory = $this->getMockBuilder(ConfigFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock()
        ;

        $configFactory->expects($this->once())->method('create')
            ->with( $this->isInstanceOf(\SplFileInfo::class))
            ->willReturn(new Config([]))
        ;

        $defFactory = $this->getMockBuilder(DefinitionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createListByConfig'])
            ->getMock()
        ;

        $defFactory->expects($this->once())->method('createListByConfig')
            ->with($this->isInstanceOf(Config::class))
            ->willReturn([])
        ;

        $loader = new DefinitionLoader($configFactory, $defFactory);
        $this->assertEquals([], $loader->load(new \SplFileInfo(__FILE__)));
    }
}