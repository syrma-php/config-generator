<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Config\Loader;


use PHPUnit\Framework\TestCase;
use Syrma\ConfigGenerator\Config\Config;

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
        $config = new Config(['def0' => ['foo' => 0], 'def1' => ['foo' => 1]]);
        $this->assertSame(['def0', 'def1' ], $config->getDefinitionIds());
        $this->assertSame(['def0' => ['foo' => 0], 'def1' => ['foo' => 1]], $config->getDefinitions());
        $this->assertSame(['foo' => 0], $config->getDefinition('def0'));
        $this->assertSame(['foo' => 1], $config->getDefinition('def1'));
    }

    public function testWithBadKey(): void
    {
        $config = new Config(['def0' => ['foo' => 0]]);
        $this->expectException(\InvalidArgumentException::class);

        $config->getDefinition('def1');
    }
}