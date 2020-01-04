<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Config\Loader;


use PHPUnit\Framework\TestCase;
use Syrma\ConfigGenerator\Config\Builder\DefinitionBuilder;
use Syrma\ConfigGenerator\Config\Config;
use Syrma\ConfigGenerator\Config\Definition;

class ConfigTest extends TestCase
{

    public function testEmpty(): void
    {
        $config = new Config([]);
        $this->assertEquals([], $config->getDefinitionIds());
        $this->assertEquals([], $config->getDefinitions());
    }

    public function testSimple(): void
    {
        $defList = [];
        foreach( ['def0', 'def1'] as $i => $defId){

            $defList[$i] = $this->getMockBuilder(Definition::class)
                ->disableOriginalConstructor()
                ->setMethods(['getId'])
                ->getMock()
            ;

            $defList[$i]->expects($this->exactly(2))->method('getId')->willReturn($defId);
        }

        $config = new Config($defList);
        $this->assertSame(['def0', 'def1' ], $config->getDefinitionIds());
        $this->assertSame(['def0' => $defList[0], 'def1' => $defList[1]], $config->getDefinitions());
        $this->assertSame('def0', $config->getDefinition('def0')->getId());
        $this->assertSame('def1', $config->getDefinition('def1')->getId());
    }

    public function testWithBadKey(): void
    {
        $def0 = $this->getMockBuilder(Definition::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock()
        ;

        $def0->expects($this->once())->method('getId')->willReturn('def0');

        $config = new Config([$def0]);

        $this->expectException(\InvalidArgumentException::class);
        $config->getDefinition('def1');
    }
}