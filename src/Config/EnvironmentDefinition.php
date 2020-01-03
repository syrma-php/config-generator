<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config;

use Syrma\ConfigGenerator\Util\ParameterBag;
use const DIRECTORY_SEPARATOR;
use Symfony\Component\Templating\TemplateReferenceInterface;

class EnvironmentDefinition
{
    /**
     * @var string
     */
    private $definitionId;

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
     * @var ParameterBag
     */
    private $parameters;

    /**
     * @param string $definitionId
     * @param string $name
     * @param TemplateReferenceInterface $template
     * @param string $outputPath
     * @param string $outputFileName
     * @param ParameterBag $parameters
     */
    public function __construct(string $definitionId, string $name, TemplateReferenceInterface $template, string $outputPath, string $outputFileName, ParameterBag $parameters)
    {
        $this->definitionId = $definitionId;
        $this->name = $name;
        $this->template = $template;
        $this->outputPath = $outputPath;
        $this->outputFileName = $outputFileName;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getDefinitionId(): string
    {
        return $this->definitionId;
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

    public function getParameters(): ParameterBag
    {
        return $this->parameters;
    }
}
