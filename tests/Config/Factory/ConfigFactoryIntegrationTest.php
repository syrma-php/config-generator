<?php
declare(strict_types=1);

namespace Syrma\ConfigGenerator\Tests\Config\Factory;

use PHPUnit\Framework\TestCase;
use Syrma\ConfigGenerator\Application;
use Syrma\ConfigGenerator\Config\ConfigFileType;
use Syrma\ConfigGenerator\Config\Definition;
use Syrma\ConfigGenerator\Config\EnvironmentDefinition;
use Syrma\ConfigGenerator\Config\Factory\ConfigFactory;


class ConfigFactoryIntegrationTest extends TestCase
{
    private const YML_TPL_EMPTY = __DIR__ . '/fixtures/yml/template/empty.vhost';
    private const YML_OUT = __DIR__ . '/fixtures/yml/out';

    /**
     * \@slowThreshold 500
     */
    public function testYmlHelloWorld(): void
    {
        $config = $this->getConfigFactory()->create(new \SplFileInfo(__DIR__ . '/fixtures/yml/conf.d/hello-world.com.yml'));
        $this->assertEquals(['hello-world.com', 'hello-world.eu'], $config->getDefinitionIds());
        $this->assertHelloWorldDef( $config->getDefinition('hello-world.com'), 'hello-world.com', ['live', 'dev']);
        $this->assertHelloWorldDef( $config->getDefinition('hello-world.eu'),'hello-world.eu',['live', 'test', 'dev']);
    }

    /**
     * \@slowThreshold 500
     */
    public function testYmlExample(): void
    {
        $config = $this->getConfigFactory()->create(new \SplFileInfo(__DIR__ . '/fixtures/yml/conf.d/example.com.yml'));
        $this->assertEquals(['example.com'], $config->getDefinitionIds());
        $this->assertExampleComDefinition( $config->getDefinition('example.com'));
    }

    /**
     * \@slowThreshold 500
     */
    public function testYmlConfig(): void
    {
        $config = $this->getConfigFactory()->create(new \SplFileInfo(__DIR__ . '/fixtures/yml/config.yml'));
        $this->assertEquals(['hello-world.hu', 'example.com', 'hello-world.com', 'hello-world.eu', ], $config->getDefinitionIds());
        $this->assertHelloWorldDef( $config->getDefinition('hello-world.com'), 'hello-world.com', ['live', 'dev']);
        $this->assertHelloWorldDef( $config->getDefinition('hello-world.eu'),'hello-world.eu',['live', 'test', 'dev']);
        $this->assertHelloWorldDef( $config->getDefinition('hello-world.hu'), 'hello-world.hu', ['live', 'dev']);
        $this->assertExampleComDefinition( $config->getDefinition('example.com'));
    }

    private function getConfigFactory(): ConfigFactory
    {
        return Application::createContainer()->get('config.factory');
    }

    private function assertHelloWorldDef(Definition $def, string $defId, array $envList ): void
    {
        $this->assertSame($defId, $def->getId());
        $this->assertSame(ConfigFileType::TYPE_NGINX, $def->getType()->getValue());
        $this->assertCount(count($envList), $envMap = $def->getEnvironmentMap());

        foreach ($envList as $env){
            $this->assertArrayHasKey($env, $envMap);
            $this->assertInstanceOf(EnvironmentDefinition::class, $envDef = $envMap[$env]);

            $this->assertSame($def->getId(), $envDef->getDefinitionId());
            $this->assertSame($env, $envDef->getName());
            $this->assertSame(self::YML_TPL_EMPTY, $envDef->getTemplate()->getLogicalName());
            $this->assertSame(self::YML_OUT, $envDef->getOutputPath());
            $this->assertSame($env.'.'.$defId. '.vhost', $envDef->getOutputFileName());

            $this->assertSame([
                EnvironmentDefinition::PARAM_ENV => $env,
                EnvironmentDefinition::PARAM_ENVIRONMENT => $env,
                EnvironmentDefinition::PARAM_DEFINITION => $defId,
            ], $envDef->getParameters()->all());
        }
    }

    private function assertExampleComDefinition(Definition $def): void
    {

        $defId = 'example.com';
        $this->assertSame($defId, $def->getId());
        $this->assertSame(ConfigFileType::TYPE_NGINX, $def->getType()->getValue());
        $this->assertCount(2, $envMap = $def->getEnvironmentMap());

        $this->assertArrayHasKey('live', $envMap);
        $this->assertInstanceOf(EnvironmentDefinition::class, $envDef = $envMap['live']);
        $this->assertSame($def->getId(), $envDef->getDefinitionId());
        $this->assertSame('live', $envDef->getName());
        $this->assertSame(self::YML_TPL_EMPTY, $envDef->getTemplate()->getLogicalName());
        $this->assertSame(self::YML_OUT, $envDef->getOutputPath());
        $this->assertSame($defId. '.vhost', $envDef->getOutputFileName());

        $this->assertSame([
            EnvironmentDefinition::PARAM_ENV => 'live',
            EnvironmentDefinition::PARAM_ENVIRONMENT => 'live',
            EnvironmentDefinition::PARAM_DEFINITION => $defId,
            'fastcgiName' => 'exampleFastCgi',
            'sessionId' => 'ESID',
            'accessLog' => true
        ], $envDef->getParameters()->all());

        $this->assertArrayHasKey('dev', $envMap);
        $this->assertInstanceOf(EnvironmentDefinition::class, $envDef = $envMap['dev']);
        $this->assertSame($def->getId(), $envDef->getDefinitionId());
        $this->assertSame('dev', $envDef->getName());
        $this->assertSame(self::YML_TPL_EMPTY, $envDef->getTemplate()->getLogicalName());
        $this->assertSame(self::YML_OUT, $envDef->getOutputPath());
        $this->assertSame('dev'.'.'.$defId. '.vhost', $envDef->getOutputFileName());

        $this->assertSame([
            EnvironmentDefinition::PARAM_ENV => 'dev',
            EnvironmentDefinition::PARAM_ENVIRONMENT => 'dev',
            EnvironmentDefinition::PARAM_DEFINITION => $defId,
            'fastcgiName' => 'exampleFastCgi',
            'sessionId' => 'ESID',
        ], $envDef->getParameters()->all());
    }
}