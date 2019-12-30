<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Command;

use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Syrma\ConfigGenerator\Config\ConfigDefinition;

class DumpReferenceCommand extends Command
{
    protected function configure()
    {
        $this->setName('dump-reference');
        $this->setDescription('Dumps the default configuration schema');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dumper = new YamlReferenceDumper();
        $config = new ConfigDefinition();

        foreach( $config->getConfigTreeBuilder()->buildTree()->getChildren()  as $child){
            $output->writeln($dumper->dumpNode($child));
        }
    }
}
