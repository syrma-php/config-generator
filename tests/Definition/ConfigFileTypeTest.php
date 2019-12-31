<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Tests\Definition;


use PHPUnit\Framework\TestCase;
use Syrma\ConfigGenerator\Definition\ConfigFileType;

class ConfigFileTypeTest extends TestCase
{

    public function testAllMap():void
    {
        $ref = new \ReflectionClass(ConfigFileType::class);
        $typeEnumList = array_filter($ref->getConstants(), static function(string $key){
            return 0 === strpos($key, 'TYPE_');
        }, ARRAY_FILTER_USE_KEY);

        $this->assertEquals(array_values($typeEnumList), ConfigFileType::ALL);
    }

    public function testCreate(): void
    {
        $objP0 = ConfigFileType::create(ConfigFileType::TYPE_PLAIN);
        $objP1 = ConfigFileType::create(ConfigFileType::TYPE_PLAIN);
        $objI0 = ConfigFileType::create(ConfigFileType::TYPE_INI);

        $this->assertEquals(ConfigFileType::TYPE_PLAIN, $objP0->getValue());
        $this->assertEquals(ConfigFileType::TYPE_PLAIN, $objP1->getValue());
        $this->assertEquals(ConfigFileType::TYPE_INI, $objI0->getValue());

        $this->assertSame($objP0, $objP1);
    }
}