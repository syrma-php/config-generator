<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Config\Loader;


use Syrma\ConfigGenerator\Config\Loader\ParameterFileLoader;
use Syrma\ConfigGenerator\Config\Loader\ParameterFileLoaderInterface;
use Syrma\ConfigGenerator\Exception\NotFoundException;
use Syrma\ConfigGenerator\Util\ParameterBag;

class ParameterFileLoaderTest extends AbstractParameterFileLoaderTest
{

    public function testEmpty(): void
    {
        $obj = new ParameterFileLoader();
        $this->assertFalse($obj->isSupported( $this->createFileRef(self::FILE_YML_EMPTY)));

        $this->expectException(NotFoundException::class);
        $obj->load($this->createFileRef(self::FILE_YML_EMPTY));
    }

    public function testDelegate(): void
    {
        $mock0 = $this->getMockBuilder(ParameterFileLoaderInterface::class)->getMock();
        $mock0->expects($this->exactly(2))->method('isSupported')->willReturn(false);
        $mock0->expects($this->never())->method('load');


        $mock1 = $this->getMockBuilder(ParameterFileLoaderInterface::class)->getMock();
        $mock1->expects($this->exactly(2))->method('isSupported')->willReturnCallback(function (\SplFileInfo $file){
            $this->assertEquals(self::FILE_YML_EMPTY, $file->getFilename());
            return true;
        });;
        $mock1->expects($this->once())->method('load')->willReturnCallback(function (\SplFileInfo $file){
            $this->assertEquals(self::FILE_YML_EMPTY, $file->getFilename());
            return new ParameterBag(['foo' => 'bar']);
        });

        $mock2 = $this->getMockBuilder(ParameterFileLoaderInterface::class)->getMock();
        $mock2->expects($this->never())->method('isSupported')->willReturn(false);
        $mock2->expects($this->never())->method('load');

        $obj = new ParameterFileLoader($mock0, $mock1, $mock2);
        $this->assertTrue($obj->isSupported( $this->createFileRef(self::FILE_YML_EMPTY)));
        $this->assertEquals(['foo' => 'bar'],$obj->load($this->createFileRef(self::FILE_YML_EMPTY))->all());
    }
}