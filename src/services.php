<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\inline;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Yaml\Parser;
use Syrma\ConfigGenerator\Command\DumpReferenceCommand;
use Syrma\ConfigGenerator\Command\GenerateCommand;
use Syrma\ConfigGenerator\Command\Handler\CheckHandler;
use Syrma\ConfigGenerator\Command\Handler\GenerateHandler;
use Syrma\ConfigGenerator\Config\Factory\ConfigFactory;
use Syrma\ConfigGenerator\Config\Factory\DefinitionBuilderFactory;
use Syrma\ConfigGenerator\Config\Factory\DefinitionFactory;
use Syrma\ConfigGenerator\Config\Loader\ConfigFileLoader;
use Syrma\ConfigGenerator\Config\Loader\ParameterFileAggregateLoader;
use Syrma\ConfigGenerator\Config\Loader\ParameterFileLoader;
use Syrma\ConfigGenerator\Config\Loader\YamlConfigFileLoader;
use Syrma\ConfigGenerator\Config\Loader\YamlParameterFileLoader;
use Syrma\ConfigGenerator\Generator\Builder\GeneratorContextFactory;
use Syrma\ConfigGenerator\Generator\Generator;
use Syrma\ConfigGenerator\Generator\HeaderGenerator\BlockCommentBaseHeaderGenerator;
use Syrma\ConfigGenerator\Generator\HeaderGenerator\HashTagBaseHeaderGenerator;
use Syrma\ConfigGenerator\Generator\HeaderGenerator\HeaderGenerator;
use Syrma\ConfigGenerator\Generator\HeaderGenerator\IniHeaderGenerator;
use Syrma\ConfigGenerator\Generator\HeaderGenerator\PhpHeaderGenerator;
use Syrma\ConfigGenerator\Generator\HeaderGenerator\XmlHeaderGenerator;
use Syrma\ConfigGenerator\Generator\Processor\CronPostProcessor;
use Syrma\ConfigGenerator\Generator\Processor\PostProcessorChain;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set('command.generator', GenerateCommand::class)
        ->public()
        ->arg(0, ref('config.factory'))
        ->arg(1, ref('command.handler.check'))
        ->arg(2, ref('command.handler.generate'))
        ->tag('command')
    ;

    $services->set('command.dump-reference', DumpReferenceCommand::class)
        ->public()
        ->tag('command')
    ;

    $services->set('command.handler.check', CheckHandler::class)
        ->private()
        ->arg(0, ref('generator.generator'))
        ->arg(1, ref('generator.context.factory'))
    ;

    $services->set('command.handler.generate', GenerateHandler::class)
        ->private()
        ->arg(0, ref('generator.generator'))
        ->arg(1, ref('generator.context.factory'))
    ;

    /*************** CONFIG *********************/
    $services->set('config.factory.definition', DefinitionFactory::class)
        ->private()
        ->arg(0, ref('symfony.filesystem'))
        ->arg(1, ref('symfony.templating.template_name_parser'))
        ->arg(2, ref('config.parameter_file.aggregate_loader'))
        ->arg(3, ref('config.factory.definition_builder'))
    ;

    $services->set('config.factory.definition_builder', DefinitionBuilderFactory::class)
        ->private()
    ;

    $services->set('config.factory', ConfigFactory::class)
        ->public()
        ->arg(0, ref('config.file.loader'))
        ->arg(1, ref('config.factory.definition'))
    ;

    $services->set('config.file.loader', ConfigFileLoader::class)
        ->private()
        ->arg(0, ref('config.file.loader.yml'))
    ;

    $services->set('config.file.loader.yml', YamlConfigFileLoader::class)
        ->private()
        ->arg(0, ref('symfony.yaml.parser'))
    ;

    $services->set('config.parameter_file.aggregate_loader', ParameterFileAggregateLoader::class)
        ->private()
        ->arg(0, ref('config.parameter_file.loader'))
    ;

    $services->set('config.parameter_file.loader', ParameterFileLoader::class)
        ->private()
        ->arg(0, ref('config.parameter_file.loader.yml'))
    ;

    $services->set('config.parameter_file.loader.yml', YamlParameterFileLoader::class)
        ->private()
        ->arg(0, ref('symfony.yaml.parser'))
    ;

    /*************** GENERATOR *********************/
    $services->set('generator.context.factory', GeneratorContextFactory::class)
        ->private()
    ;

    $services->set('generator.generator', Generator::class)
        ->private()
        ->arg(0, ref('symfony.filesystem'))
        ->arg(1, ref('symfony.templating.engine'))
        ->arg(2, ref('generator.header'))
        ->arg(3, ref('generator.processor.post_processor.chain'))
    ;

    $services->set('generator.header', HeaderGenerator::class)
        ->private()
        ->arg(0, inline(BlockCommentBaseHeaderGenerator::class))
        ->arg(1, inline(HashTagBaseHeaderGenerator::class))
        ->arg(2, inline(IniHeaderGenerator::class))
        ->arg(3, inline(XmlHeaderGenerator::class))
        ->arg(4, inline(PhpHeaderGenerator::class))
    ;

    $services->set('generator.processor.post_processor.chain', PostProcessorChain::class)
        ->private()
        ->arg(0, inline(CronPostProcessor::class))
    ;

    /*************** SYMFONY *********************/
    $services->set('symfony.yaml.parser', Parser::class)
        ->private()
    ;

    $services->set('symfony.filesystem', Filesystem::class)
        ->private()
    ;

    $services->set('symfony.templating.template_name_parser', TemplateNameParser::class)
        ->private()
    ;

    $services->set('symfony.templating.engine', DelegatingEngine::class)
        ->private()
        ->arg(0, [ref('symfony.templating.engine.php')])
    ;

    $services->set('symfony.templating.engine.php', PhpEngine::class)
        ->private()
        ->arg(0, ref('symfony.templating.template_name_parser'))
        ->arg(1, ref('symfony.templating.loader'))
        ->arg(2, [inline(SlotsHelper::class)])
    ;

    $services->set('symfony.templating.loader', FilesystemLoader::class)
        ->private()
        ->arg(0, '%%name%%.%%engine%%')
    ;
};
