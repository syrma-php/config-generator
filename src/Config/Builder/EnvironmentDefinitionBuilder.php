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
     * @param DefinitionBuilder $definitionBuilder
     * @param string $name
     */
    public function __construct(DefinitionBuilder $definitionBuilder, string $name)
    {
        $this->definitionBuilder = $definitionBuilder;
        $this->name = $name;
    }

    /**
     * @return DefinitionBuilder
     */
    public function getDefinitionBuilder(): DefinitionBuilder
    {
        return $this->definitionBuilder;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param TemplateReferenceInterface $template
     * @return EnvironmentDefinitionBuilder
     */
    public function setTemplate(TemplateReferenceInterface $template): EnvironmentDefinitionBuilder
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @param string $outputPath
     * @return EnvironmentDefinitionBuilder
     */
    public function setOutputPath(string $outputPath): EnvironmentDefinitionBuilder
    {
        $this->outputPath = $outputPath;
        return $this;
    }

    /**
     * @param string $outputFileName
     * @return EnvironmentDefinitionBuilder
     */
    public function setOutputFileName(string $outputFileName): EnvironmentDefinitionBuilder
    {
        $this->outputFileName = $outputFileName;
        return $this;
    }

    /**
     * @param ParameterBag $parameters
     * @return EnvironmentDefinitionBuilder
     */
    public function setParameters(ParameterBag $parameters): EnvironmentDefinitionBuilder
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