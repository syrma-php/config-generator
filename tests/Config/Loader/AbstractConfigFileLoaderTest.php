<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Config\Loader;


use PHPUnit\Framework\TestCase;

abstract class AbstractConfigFileLoaderTest extends TestCase
{

    protected function createFileRef( string $name): \SplFileInfo
    {
        return new \SplFileInfo( __DIR__ . '/fixtures/' . $name);
    }
}