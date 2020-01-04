<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config;

use const DIRECTORY_SEPARATOR;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Syrma\ConfigGenerator\Util\ParameterBag;

class EnvironmentDefinition
{
    public const PARAM_ENV = 'env';
    public const PARAM_ENVIRONMENT = 'environment';
    public const PARAM_DEFINITION = 'definition';

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

    public function __construct(string $definitionId, string $name, TemplateReferenceInterface $template, string $outputPath, string $outputFileName, ParameterBag $parameters)
    {
        $this->definitionId = $definitionId;
        $this->name = $name;
        $this->template = $template;
        $this->outputPath = $outputPath;
        $this->outputFileName = $outputFileName;
        $this->parameters = $parameters;
    }

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
