<?php

namespace Yamadashy\TryFiber;

use Fiber;
use Throwable;

require_once __DIR__.'/../vendor/autoload.php';

class FiberSample
{

    private const FILE_NAMES = [
        'fileA.txt',
        'fileB.txt',
        'fileC.txt'
    ];

    /**
     * @return void
     * @throws Throwable
     */
    public static function sample(): void
    {
        $fiber = new Fiber(function (): void {
            $value = Fiber::suspend('fiber!!!');
            echo "resume: ", $value, "\n";
        });

        $value = $fiber->start();

        echo "suspending: ", $value, "\n";

        $fiber->resume('test');
    }

    /**
     * @return void
     * @throws Throwable
     */
    public static function loadFilesSerial(): void
    {
        $fibers = [];

        foreach (self::FILE_NAMES as $fileName){
            $filePath = __DIR__ . '/../resources/'.$fileName;

            $fibers[] = FileLoader::getFileContent($filePath);
        }

        foreach ($fibers as $fiber) {
            [$filePath, $fileContent] = FiberUtil::wait($fiber, function($filePath) {
//                echo "suspending: ".$filePath."\n";
            });
            echo "loaded: $filePath\n";
        }
    }

    /**
     * @return void
     * @throws Throwable
     */
    public static function loadFilesParallel(): void
    {
        $fibers = [];

        foreach (self::FILE_NAMES as $fileName){
            $filePath = __DIR__ . '/../resources/'.$fileName;

            $fibers[] = FileLoader::getFileContent($filePath);
        }

        FiberUtil::waitAll($fibers, function($result) {
            [$filePath, $fileContent] = $result;
            echo "loaded: $filePath\n";
        }, function($filePath) {
//            echo "suspending: ".$filePath."\n";
        });
    }

}

echo "--- sample ---\n";
FiberSample::sample();


echo "\n";
echo "--- loadFilesSerial ---\n";
FiberSample::loadFilesSerial();

echo "\n";
echo "--- loadFilesParallel ---\n";
FiberSample::loadFilesParallel();
