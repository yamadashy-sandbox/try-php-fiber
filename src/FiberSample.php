<?php

namespace Yamadashy\TryFiber;

class FiberSample
{

    public static function run()
    {
        $fiber = new \Fiber(function (): void {
            $value = \Fiber::suspend('fiber');
            echo "resume: ", $value, "\n";
        });

        $value = $fiber->start();

        echo "suspending: ", $value, "\n";

        $fiber->resume('test');
    }

}

FiberSample::run();
