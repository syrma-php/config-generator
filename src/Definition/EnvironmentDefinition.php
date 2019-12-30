<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Definition;

use const DIRECTORY_SEPARATOR;
use Symfony\Component\Templating\TemplateReferenceInterface;

class EnvironmentDefinition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var TemplateReferenceInterface
     */
    private $template;

    /**
     * @var string
     */
    private $outputPath;

    /**
     * @var string
     */
    private $outputFileName;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(string $name, TemplateReferenceInterface $template, string $outputPath, string $outputFileName, array $parameters)
    {
        $this->name = $name;
        $this->template = $template;
        $this->outputPath = $outputPath;
        $this->outputFileName = $outputFileName;
        $this->parameters = $parameters;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTemplate(): TemplateReferenceInterface
    {
        return $this->template;
    }

    public function getOutputPath(): string
    {
        return $this->outputPath;
    }

    public function getOutputFileName(): string
    {
        return $this->outputFileName;
    }

    public function getOutputFile(): string
    {
        return $this->outputPath.DIRECTORY_SEPARATOR.$this->outputFileName;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
