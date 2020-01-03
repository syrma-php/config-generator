<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Config\Builder;

use Symfony\Component\Templating\TemplateReferenceInterface;
use Syrma\ConfigGenerator\Config\EnvironmentDefinition;
use Syrma\ConfigGenerator\Util\ParameterBag;
use Webmozart\Assert\Assert;

class EnvironmentDefinitionBuilder
{
    /**
     * @var DefinitionBuilder
     */
    private $definitionBuilder;

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
     * EnvironmentDefinitionBuilder constructor.
     */
    public function __construct(DefinitionBuilder $definitionBuilder, string $name)
    {
        $this->definitionBuilder = $definitionBuilder;
        $this->name = $name;
    }

    public function getDefinitionBuilder(): DefinitionBuilder
    {
        return $this->definitionBuilder;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setTemplate(TemplateReferenceInterface $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function setOutputPath(string $outputPath): self
    {
        $this->outputPath = $outputPath;

        return $this;
    }

    public function setOutputFileName(string $outputFileName): self
    {
        $this->outputFileName = $outputFileName;

        return $this;
    }

    public function setParameters(ParameterBag $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function getEnvironmentDefinition(): EnvironmentDefinition
    {
        Assert::notNull($this->template);
        Assert::notNull($this->outputPath);
        Assert::notNull($this->outputFileName);
        Assert::notNull($this->parameters);

        return new EnvironmentDefinition(
            $this->definitionBuilder->getId(),
            $this->name,
            $this->template,
            $this->outputPath,
            $this->outputFileName,
            $this->parameters
        );
    }
}
