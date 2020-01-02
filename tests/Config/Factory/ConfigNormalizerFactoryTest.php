<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Config\Factory;


use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Syrma\ConfigGenerator\Config\Factory\ConfigNormalizerFactory;

class ConfigNormalizerFactoryTest extends TestCase
{

    public function testCreate(): void
    {
        $factory = new ConfigNormalizerFactory(new Filesystem());
        $this->assertNotNull($factory->create([]));
    }

}