<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Command\Handler;


use Symfony\Component\Console\Style\SymfonyStyle;
use Syrma\ConfigGenerator\Config\Definition;
use Syrma\ConfigGenerator\Generator\Builder\GeneratorContextFactory;
use Syrma\ConfigGenerator\Generator\Generator;

class GenerateHandler
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var GeneratorContextFactory
     */
    private $contextFactory;

    public function __construct(Generator $generator, GeneratorContextFactory $contextFactory)
    {
        $this->generator = $generator;
        $this->contextFactory = $contextFactory;
    }

    /**
     * @param Definition[] $definitionList
     * @param SymfonyStyle $io - for console writing
     */
    public function handle(array $definitionList, SymfonyStyle $io): void
    {
        foreach ($definitionList as $definition) {
            $io->writeln(sprintf('Start generating <info>%s</info> definition environments:', $definition->getId()));

            foreach ($definition->getEnvironmentMap() as $env) {
                $io->write(sprintf('    <info>%s</info> environment ...', $env->getName()));

                try {
                    $this->generator->generate($this->contextFactory->createContext($io, $definition, $env));
                    $io->writeln(' <comment>OK</comment>');
                } catch (\Exception $ex) {
                    $io->writeln(sprintf('<error>Error: %s</error>', $ex->getMessage()));
                }
            }
        }
    }
}