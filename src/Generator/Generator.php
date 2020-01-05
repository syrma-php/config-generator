<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Generator;

use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Templating\EngineInterface;
use Syrma\ConfigGenerator\Exception\InvalidStateException;
use Syrma\ConfigGenerator\Generator\HeaderGenerator\HeaderGeneratorInterface;
use Syrma\ConfigGenerator\Generator\Processor\PostProcessorInterface;

class Generator
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @var HeaderGeneratorInterface
     */
    private $headerGenerator;

    /**
     * @var PostProcessorInterface
     */
    private $postProcessor;

    public function __construct(Filesystem $fs, EngineInterface $engine, HeaderGeneratorInterface $headerGenerator, PostProcessorInterface $postProcessor)
    {
        $this->fs = $fs;
        $this->engine = $engine;
        $this->headerGenerator = $headerGenerator;
        $this->postProcessor = $postProcessor;
    }

    public function generate(GeneratorContext $context): void
    {
        $content = $this->generateContent($context);
        $this->fs->dumpFile(
            $context->getEnvironment()->getOutputFile(),
            $this->generateHeader($content, $context).$content
        );
    }

    public function check(GeneratorContext $context): void
    {
        $this->checkModified($context);
        $this->generateContent($context); //try generation ...
    }

    private function generateContent(GeneratorContext $context): string
    {
        $env = $context->getEnvironment();
        $content = $this->engine->render($env->getTemplate(), $env->getParameters()->all());

        return $this->postProcessor->process($content, $context);
    }

    private function generateHeader(string $content, GeneratorContext $context): string
    {
        return $this->headerGenerator->generateHeader($content, $context);
    }

    private function checkModified(GeneratorContext $context): void
    {
        $outputFile = $context->getEnvironment()->getOutputFile();
        if (false === $this->fs->exists($outputFile)) {
            return;
        }

        $content = $this->getFileContent($outputFile);
        if (true === $this->headerGenerator->isModified($content, $context)) {
            throw new InvalidStateException(sprintf('The configuration file "%s" was modified by manually!', $outputFile));
        }
    }

    private function getFileContent(string $file): string
    {
        set_error_handler(static function ($type, $msg) use (&$error) { $error = $msg; });
        $content = file_get_contents($file);
        restore_error_handler();
        if (false === $content) {
            throw new RuntimeException($error);
        }

        return $content;
    }
}
