<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Config\Loader;


use Syrma\ConfigGenerator\Config\Loader\ConfigFileLoader;
use Syrma\ConfigGenerator\Config\Loader\ConfigFileLoaderInterface;
use Syrma\ConfigGenerator\Exception\NotFoundException;

class ConfigFileLoaderTest extends AbstractConfigFileLoaderTest
{

    public function testEmpty(): void
    {
        $loader = new ConfigFileLoader();
        $this->assertFalse($loader->isSupported($this->createFileRef(self::FILE_YML_EMPTY)));

        $this->expectException(NotFoundException::class);
        $loader->load($this->createFileRef(self::FILE_YML_EMPTY));
    }

    public function testDelegate(): void
    {
        $mock0 = $this->getMockBuilder(ConfigFileLoaderInterface::class)->getMock();
        $mock0->expects($this->exactly(2))->method('isSupported')->willReturn(false);
        $mock0->expects($this->never())->method('load');


        $mock1 = $this->getMockBuilder(ConfigFileLoaderInterface::class)->getMock();
        $mock1->expects($this->exactly(2))->method('isSupported')->willReturnCallback(function (\SplFileInfo $file){
            $this->assertEquals(self::FILE_YML_EMPTY, $file->getFilename());
            return true;
        });;
        $mock1->expects($this->once())->method('load')->willReturnCallback(function (\SplFileInfo $file){
            $this->assertEquals(self::FILE_YML_EMPTY, $file->getFilename());
            return ['foo' => 'bar'];
        });

        $mock2 = $this->getMockBuilder(ConfigFileLoaderInterface::class)->getMock();
        $mock2->expects($this->never())->method('isSupported')->willReturn(false);
        $mock2->expects($this->never())->method('load');

        $obj = new ConfigFileLoader($mock0, $mock1, $mock2);
        $this->assertTrue($obj->isSupported( $this->createFileRef(self::FILE_YML_EMPTY)));
        $this->assertEquals(['foo' => 'bar'],$obj->load($this->createFileRef(self::FILE_YML_EMPTY)));
    }
}