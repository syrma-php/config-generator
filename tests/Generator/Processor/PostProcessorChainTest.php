<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Tests\Generator\Processor;

use PHPUnit\Framework\TestCase;
use Syrma\ConfigGenerator\Generator\GeneratorContext;
use Syrma\ConfigGenerator\Generator\Processor\PostProcessorChain;
use Syrma\ConfigGenerator\Generator\Processor\PostProcessorInterface;

class PostProcessorChainTest extends TestCase
{
    public function testEmpty(): void
    {
        $context = $this->getMockBuilder(GeneratorContext::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $proc = new PostProcessorChain();
        $this->assertFalse($proc->isSupported($context));
        $this->assertSame('foo', $proc->process('foo', $context));
    }

    public function testChain(): void
    {
        $proc0 = $this->getMockBuilder(PostProcessorInterface::class)->getMock();
        $proc0->expects($this->exactly(2))
            ->method('isSupported')
            ->with($this->isInstanceOf(GeneratorContext::class))
            ->willReturn(true);

        $proc0->expects($this->once())->method('process')
            ->willReturnCallback(static function (string $content, GeneratorContext $context) {
                return $content.'-PROC0';
            })
        ;

        $proc1 = $this->getMockBuilder(PostProcessorInterface::class)->getMock();
        $proc1->expects($this->once())->method('isSupported')->willReturn(false);
        $proc1->expects($this->never())->method('process');

        $proc2 = $this->getMockBuilder(PostProcessorInterface::class)->getMock();
        $proc2->expects($this->once())
            ->method('isSupported')
            ->with($this->isInstanceOf(GeneratorContext::class))
            ->willReturn(true);

        $proc2->expects($this->once())->method('process')
            ->willReturnCallback(static function (string $content, GeneratorContext $context) {
                return $content.'-PROC2';
            })
        ;

        $context = $this->getMockBuilder(GeneratorContext::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $proc = new PostProcessorChain($proc0, $proc1, $proc2);
        $this->assertTrue($proc->isSupported($context));
        $this->assertSame('foo-PROC0-PROC2', $proc->process('foo', $context));
    }
}
