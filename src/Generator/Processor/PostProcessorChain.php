<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Generator\Processor;

use Syrma\ConfigGenerator\Generator\GeneratorContext;

class PostProcessorChain implements PostProcessorInterface
{
    /**
     * @var PostProcessorInterface[]
     */
    private $processes;

    /**
     * @param PostProcessorInterface[] $processes
     */
    public function __construct(PostProcessorInterface ...$processes)
    {
        $this->processes = $processes;
    }

    public function isSupported(GeneratorContext $context): bool
    {
        foreach ($this->processes as $proc) {
            if (true === $proc->isSupported($context)) {
                return true;
            }
        }

        return false;
    }

    public function process(string $content, GeneratorContext $context): string
    {
        foreach ($this->processes as $proc) {
            if (true === $proc->isSupported($context)) {
                $content = $proc->process($content, $context);
            }
        }

        return $content;
    }
}
