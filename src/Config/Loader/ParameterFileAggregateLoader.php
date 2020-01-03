<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Config\Loader;


use SplFileInfo;
use Syrma\ConfigGenerator\Util\ParameterBag;

class ParameterFileAggregateLoader implements ParameterFileLoaderInterface
{
    /**
     * @var ParameterFileLoaderInterface
     */
    private $innerLoader;

    /**
     * @param ParameterFileLoaderInterface $innerLoader
     */
    public function __construct(ParameterFileLoaderInterface $innerLoader)
    {
        $this->innerLoader = $innerLoader;
    }

    public function isSupported(SplFileInfo $file): bool
    {
        return $this->innerLoader->isSupported($file);
    }

    public function load(SplFileInfo $file): ParameterBag
    {
        return $this->innerLoader->load($file);
    }

    public function loadByList( SplFileInfo ...$files): ParameterBag
    {
        $paramBag = new ParameterBag([]);
        foreach ($files as $file){
            $paramBag->append( $this->load($file));
        }

        return $paramBag;
    }

}