<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Config\Factory;


use Syrma\ConfigGenerator\Config\Builder\DefinitionBuilder;

class DefinitionBuilderFactory
{
    public function create( string $definitionId ) : DefinitionBuilder
    {
        return new DefinitionBuilder($definitionId);
    }
}