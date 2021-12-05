<?php

namespace Yamadashy\TryFiber;

use Fiber;

class FileLoader
{
    /**
     * @param string $filePath
     * @return Fiber
     */
    public static function getFileContent(string $filePath): Fiber
    {
        return new Fiber(function() use ($filePath) {
            $fileContent = '';
            $fp = fopen($filePath, 'rb');
            Fiber::suspend();

            while (!feof($fp)) {
                $chunk = 1024;
                Fiber::suspend($filePath);
                $fileContent .= fread($fp, $chunk);
            }

            fclose($fp);

            return [$filePath, $fileContent];
        });
    }

}
