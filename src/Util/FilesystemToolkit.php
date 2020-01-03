<?php
declare(strict_types=1);


namespace Syrma\ConfigGenerator\Util;


class FilesystemToolkit
{
    public static function isWritableAnyPath( string $basePath ): bool
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
}