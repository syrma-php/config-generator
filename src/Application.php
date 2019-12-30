<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator;

use PackageVersions\Versions;
use function array_keys;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as SfApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;
use Webmozart\Assert\Assert;

class Application extends SfApplication
{
    private const LOGO = <<<LOGO
  ____                                ____             __ _          ____                           _             
 / ___| _   _ _ __ _ __ ___   __ _   / ___|___  _ __  / _(_) __ _   / ___| ___ _ __   ___ _ __ __ _| |_ ___  _ __ 
 \___ \| | | | '__| '_ ` _ \ / _` | | |   / _ \| '_ \| |_| |/ _` | | |  _ / _ \ '_ \ / _ \ '__/ _` | __/ _ \| '__|
  ___) | |_| | |  | | | | | | (_| | | |__| (_) | | | |  _| | (_| | | |_| |  __/ | | |  __/ | | (_| | || (_) | |   
 |____/ \__, |_|  |_| |_| |_|\__,_|  \____\___/|_| |_|_| |_|\__, |  \____|\___|_| |_|\___|_|  \__,_|\__\___/|_|   
        |___/                                               |___/                                                 


LOGO;
    /**
     * @var string
     */
    private $releaseDate;

    public function __construct(string $releaseDate = '')
    {
        parent::__construct('Syrma Config Generator', $this->guessVersion());
        $this->releaseDate = $releaseDate;
    }

    public function getLongVersion()
    {
        return trim(
            sprintf(
                '<info>%s</info> version <comment>%s</comment> %s',
                $this->getName(),
                $this->getVersion(),
                false === strpos($this->releaseDate, '@') ? $this->releaseDate : ''
            )
        );
    }

    public function getHelp()
    {
        return self::LOGO . parent::getHelp();
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $container = $this->createContainer();

        foreach (array_keys($container->findTaggedServiceIds('command')) as $id) {
            /* @var Command $command */
            Assert::isInstanceOf($command = $container->get($id), Command::class);
            $commands[] = $command;
        }

        return $commands;
    }


    private function createContainer(): TaggedContainerInterface
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__));
        $loader->load('services.php');
        $containerBuilder->compile();

        return $containerBuilder;
    }

    /**
     * @return string
     */
    private function guessVersion(): string
    {
        $rawVersion = Versions::getVersion('syrma/config-generator');
        [$prettyVersion, $commitHash] = explode('@', $rawVersion);
        return $prettyVersion . '@' . substr($commitHash, 0, 7);
    }
}
