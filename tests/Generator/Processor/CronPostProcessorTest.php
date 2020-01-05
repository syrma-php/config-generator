<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Generator\Processor;

use PHPUnit\Framework\TestCase;
use Syrma\ConfigGenerator\Config\ConfigFileType;
use Syrma\ConfigGenerator\Config\Definition;
use Syrma\ConfigGenerator\Generator\GeneratorContext;
use Syrma\ConfigGenerator\Generator\Processor\CronPostProcessor;

class CronPostProcessorTest extends TestCase
{
    public function testIsSupported(): void
    {
        $proc = new CronPostProcessor();
        $this->assertFalse($proc->isSupported($this->createContext(ConfigFileType::TYPE_NGINX)));
        $this->assertTrue($proc->isSupported($this->createContext(ConfigFileType::TYPE_CRON)));
    }

    public function testProcess(): void
    {
        $proc = new CronPostProcessor();
        $this->assertSame(
            'Foo' . PHP_EOL,
            $proc->process('Foo', $this->createContext(ConfigFileType::TYPE_CRON))
        );
    }

    protected function createContext( string $type): GeneratorContext
    {
        $def = $this->getMockBuilder(Definition::class)
            ->disableOriginalConstructor()
            ->getMock();
        $def->method('getType')->willReturn(ConfigFileType::create($type));

        $context = $this->getMockBuilder(GeneratorContext::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context->method('getDefinition')->willReturn($def);

        return $context;
    }

}