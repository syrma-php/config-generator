<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Command;

use const DIRECTORY_SEPARATOR;
use Exception;
use SplFileInfo;
use function sprintf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Syrma\ConfigGenerator\Definition\Definition;
use Syrma\ConfigGenerator\Definition\DefinitionLoader;
use Syrma\ConfigGenerator\Exception\NotFoundException;
use Syrma\ConfigGenerator\Generator\Builder\GeneratorContextFactory;
use Syrma\ConfigGenerator\Generator\Generator;

class GenerateCommand extends Command
{
    private const ARG_DEFINITION_FILE = 'definitionFile';
    private const OPT_DRY_RUN = 'dry-run';
    private const OPT_FORCE = 'force';
    public const OPT_DEFINITION = 'definition';

    /**
     * @var DefinitionLoader
     */
    private $definitionLoader;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var GeneratorContextFactory
     */
    private $contextFactory;

    public function __construct(DefinitionLoader $definitionLoader, Generator $generator, Filesystem $fs, GeneratorContextFactory $contextBuilder)
    {
        parent::__construct();
        $this->definitionLoader = $definitionLoader;
        $this->generator = $generator;
        $this->fs = $fs;
        $this->contextFactory = $contextBuilder;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('generate')
            ->setDescription('Generate configuration file by definition')
            ->addArgument(self::ARG_DEFINITION_FILE, InputArgument::REQUIRED, 'Definition file')
            ->addOption(self::OPT_FORCE, 'f', InputOption::VALUE_NONE, 'Force mode, not check before generating.')
            ->addOption(self::OPT_DRY_RUN, null, InputOption::VALUE_NONE, 'Run checks only.')
            ->addOption(self::OPT_DEFINITION, 'd', InputOption::VALUE_OPTIONAL, 'One definition')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $definitionList = $this->definitionLoader->load($this->resolveConfigFile($input->getArgument(self::ARG_DEFINITION_FILE)));
        $io = new SymfonyStyle($input, $output);

        if (!empty($definition = $input->getOption(self::OPT_DEFINITION))) {
            $definitionList = $this->filterDefinitions($definition, $definitionList);
        }

        if (false === $input->getOption(self::OPT_FORCE) && true === $this->executeCheck($definitionList, $io)) {
            return 1;
        }

        $io->writeln('');

        if (false === $input->getOption(self::OPT_DRY_RUN)) {
            $this->executeGenerate($definitionList, $io);
        }

        return 0;
    }

    private function resolveConfigFile(string $fileName): SplFileInfo
    {
        $files = [
            $fileName,
            getcwd().DIRECTORY_SEPARATOR.$fileName,
        ];

        foreach ($files as $file) {
            if ($this->fs->exists($file)) {
                return new SplFileInfo($file);
            }
        }

        throw new NotFoundException(sprintf('Not found the config file: %s', $fileName));
    }

    private function executeCheck(array $definitionList, SymfonyStyle $io): bool
    {
        $hasProblem = false;

        /** @var Definition[] $definitionList */
        foreach ($definitionList as $definition) {
            $io->writeln(sprintf('Start checking <info>%s</info> definition environments:', $definition->getId()));

            foreach ($definition->getEnvironmentMap() as $env) {
                $io->write(sprintf('    <info>%s</info> environment ...', $env->getName()));

                try {
                    $this->generator->check($this->contextFactory->createContext($io, $definition, $env));
                    $io->writeln(' <comment>OK</comment>');
                } catch (Exception $ex) {
                    $io->writeln(sprintf('<error> Error: %s</error>', $ex->getMessage()));
                    $hasProblem = true;
                }
            }
        }

        return $hasProblem;
    }

    private function executeGenerate(array $definitionList, SymfonyStyle $io): void
    {
        /** @var Definition[] $definitionList */
        foreach ($definitionList as $definition) {
            $io->writeln(sprintf('Start generating <info>%s</info> definition environments:', $definition->getId()));

            foreach ($definition->getEnvironmentMap() as $env) {
                $io->write(sprintf('    <info>%s</info> environment ...', $env->getName()));

                try {
                    $this->generator->generate($this->contextFactory->createContext($io, $definition, $env));
                    $io->writeln(' <comment>OK</comment>');
                } catch (Exception $ex) {
                    $io->writeln(sprintf('<error>Error: %s</error>', $ex->getMessage()));
                }
            }
        }
    }

    private function filterDefinitions(string $definitionId, array $definitionList): array
    {
        /** @var Definition[] $definitionList */
        foreach ($definitionList as $definition) {
            if ($definition->getId() === $definitionId) {
                return [$definition];
            }
        }

        throw new NotFoundException(sprintf('The "%s" definition not found in configuration files!', $definitionId));
    }
}
