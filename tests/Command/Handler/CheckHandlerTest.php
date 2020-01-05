<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Command\Handler;


use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Syrma\ConfigGenerator\Command\Handler\CheckHandler;
use Syrma\ConfigGenerator\Config\ConfigFileType;
use Syrma\ConfigGenerator\Config\Definition;
use Syrma\ConfigGenerator\Config\EnvironmentDefinition;
use Syrma\ConfigGenerator\Generator\Builder\GeneratorContextFactory;
use Syrma\ConfigGenerator\Generator\Generator;
use Syrma\ConfigGenerator\Generator\GeneratorContext;

class CheckHandlerTest extends TestCase
{
    public function testHandle():void
    {
        $env = $this->getMockBuilder(EnvironmentDefinition::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $type = ConfigFileType::create(ConfigFileType::TYPE_NGINX);

        $io = $this->getMockBuilder(SymfonyStyle::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = $this->getMockBuilder(Generator::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator
            ->expects($this->exactly(6))
            ->method('check')
            ->with( $this->isInstanceOf(GeneratorContext::class))
        ;

        $handler = new CheckHandler($generator, new GeneratorContextFactory());
        $result = $handler->handle([
            new Definition('def0', $type, [$env, $env]),
            new Definition('def1', $type, [$env, $env]),
            new Definition('def2', $type, [$env, $env]),
        ], $io);

        $this->assertFalse($result);
    }

    public function testHandleWithError():void
    {
        $env = $this->getMockBuilder(EnvironmentDefinition::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $type = ConfigFileType::create(ConfigFileType::TYPE_NGINX);

        $io = $this->getMockBuilder(SymfonyStyle::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = $this->getMockBuilder(Generator::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator
            ->expects($this->exactly(6))
            ->method('check')
            ->willReturnCallback(static function(GeneratorContext $context){
                if( 'def1' === $context->getDefinition()->getId() ){
                    throw new \Exception('Dummy');
                }
            });


        $handler = new CheckHandler($generator, new GeneratorContextFactory());
        $result = $handler->handle([
            new Definition('def0', $type, [$env, $env]),
            new Definition('def1', $type, [$env, $env]),
            new Definition('def2', $type, [$env, $env]),
        ], $io);

        $this->assertTrue($result);
    }
}