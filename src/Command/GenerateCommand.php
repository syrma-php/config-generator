<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Syrma\ConfigGenerator\Command\Handler\CheckHandler;
use Syrma\ConfigGenerator\Command\Handler\GenerateHandler;
use Syrma\ConfigGenerator\Config\Factory\ConfigFactory;
use Syrma\ConfigGenerator\Util\FilesystemToolkit;

class GenerateCommand extends Command
{
    private const ARG_DEFINITION_FILE = 'definitionFile';
    private const OPT_DRY_RUN = 'dry-run';
    private const OPT_FORCE = 'force';
    private const OPT_DEFINITION = 'definition';

    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * @var CheckHandler
     */
    private $checkHandler;

    /**
     * @var GenerateHandler
     */
    private $generateHandler;

    public function __construct(ConfigFactory $configFactory, CheckHandler $checkHandler, GenerateHandler $generateHandler)
    {
        parent::__construct();
        $this->configFactory = $configFactory;
        $this->checkHandler = $checkHandler;
        $this->generateHandler = $generateHandler;
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
        $io = new SymfonyStyle($input, $output);
        $config = $this->configFactory->create(FilesystemToolkit::resolveFile($input->getArgument(self::ARG_DEFINITION_FILE)));

        $definitionList = !empty($definitionId = $input->getOption(self::OPT_DEFINITION)) ?
            [$config->getDefinition($definitionId)] : $config->getDefinitions();

        if (false === $input->getOption(self::OPT_FORCE) && true === $this->checkHandler->handle($definitionList, $io)) {
            return 1;
        }

        $io->writeln('');

        if (false === $input->getOption(self::OPT_DRY_RUN)) {
            $this->generateHandler->handle($definitionList, $io);
        }

        return 0;
    }
}
