<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Util;

use Webmozart\PathUtil\Path;
use function dirname;

class FilesystemToolkit
{
    public static function isWritableAnyPath(string $basePath): bool
    {
        $prevPath = null;
        $currPath = $basePath;
        while ($prevPath !== $currPath) {
            if (false !== $real = realpath($currPath)) {
                $currPath = $real;
            }

            if (true === is_dir($currPath) && true === is_writable($currPath)) {
                return true;
            }

            $prevPath = $currPath;
            $currPath = dirname($currPath);
        }

        return false;
    }

    public static function resolveFile( string $fileName, string $basePath = null ): \SplFileInfo
    {
        if( null === $basePath ){
            $basePath = getcwd();
        }
        $file = Path::canonicalize(Path::isAbsolute($fileName) ? $fileName : $basePath.\DIRECTORY_SEPARATOR.$fileName);

        if( false === file_exists($file) ){
            throw new \InvalidArgumentException(sprintf('The file (%s) is not valid name or not found! ( search: %s)', $fileName, $file));
        }

        return  new \SplFileInfo($file);
    }
}
