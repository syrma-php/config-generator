<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Tests\Generator\HeaderGenerator;

use PHPUnit\Framework\TestCase;
use function strlen;
use Symfony\Component\Console\Style\SymfonyStyle;
use Syrma\ConfigGenerator\Config\ConfigFileType;
use Syrma\ConfigGenerator\Config\Definition;
use Syrma\ConfigGenerator\Config\EnvironmentDefinition;
use Syrma\ConfigGenerator\Exception\InvalidStateException;
use Syrma\ConfigGenerator\Exception\NotFoundException;
use Syrma\ConfigGenerator\Generator\GeneratorContext;
use Syrma\ConfigGenerator\Generator\HeaderGenerator\AbstractHeaderGenerator;

class AbstractHeaderGeneratorImplTest extends TestCase
{
    private const PATH_FIXTURES = __DIR__.'/fixtures/abstractHeaderGenerator';

    public function testIsSupported(): void
    {
        $gen = $this->getGenerator();

        $this->assertTrue($gen->isSupported($this->createContext(ConfigFileType::TYPE_PLAIN)));
        $this->assertTrue($gen->isSupported($this->createContext(ConfigFileType::TYPE_NGINX)));
        $this->assertFalse($gen->isSupported($this->createContext(ConfigFileType::TYPE_CRON)));
    }

    public function testGenerateHeader(): void
    {
        $result = $this->getGenerator()->generateHeader('FOO', $this->createContext());
        $this->assertStringEqualsFile(self::PATH_FIXTURES.'/testGenerateHeader.txt', $result);
    }

    public function testIsModified(): void
    {
        $gen = $this->getGenerator();
        $context = $this->createContext();

        $content = $gen->generateHeader('FOO', $context).'FOO';
        $this->assertFalse($gen->isModified($content, $context));
        $this->assertTrue($gen->isModified($content.'@BAR', $context));
    }

    public function testIsModifiedWithNoTag(): void
    {
        $gen = $this->getGenerator();
        $context = $this->createContext();

        $content = $gen->generateHeader('FOO', $context);
        $content = substr($content, 0, (strlen($content) - 8));

        $this->expectException(InvalidStateException::class);
        $gen->isModified($content, $context);
    }

    public function testIsModifiedWithNoHash(): void
    {
        $gen = $this->getGenerator();
        $context = $this->createContext();

        $this->expectException(NotFoundException::class);
        $gen->isModified(file_get_contents(self::PATH_FIXTURES.'/testIsModifiedWithNoHash.txt'), $context);
    }

    private function getGenerator(): AbstractHeaderGenerator
    {
        return new class() extends AbstractHeaderGenerator {
            protected function getSupportedTypes(): array
            {
                return [ConfigFileType::TYPE_PLAIN, ConfigFileType::TYPE_NGINX];
            }

            protected function wrapLine(string $line): string
            {
                return '@'.$line.'@';
            }
        };
    }

    private function createContext(string $type = ConfigFileType::TYPE_PLAIN): GeneratorContext
    {
        $io = $this->getMockBuilder(SymfonyStyle::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $env = $this->getMockBuilder(EnvironmentDefinition::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $env->method('getName')->willReturn('env0');

        $def = new Definition('def0', ConfigFileType::create($type), [$env]);

        return new GeneratorContext($io, $def, $env);
    }
}
