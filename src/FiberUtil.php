<?php

namespace Yamadashy\TryFiber;

use Fiber;
use Throwable;

class FiberUtil
{

    /**
     * @param Fiber $fiber
     * @param callable|null $onResume
     * @return mixed
     * @throws Throwable
     */
    public static function wait(Fiber $fiber, callable $onResume = null): mixed
    {
        $fiber->start();

        while ($fiber->isTerminated() === false) {
            $suspendValue = $fiber->resume();

            if ($onResume !== null) {
                $onResume($suspendValue);
            }

            if ($fiber->isTerminated()) {
                break;
            }
        }

        return $fiber->getReturn();
    }

    /**
     * @param Fiber[] $fibers
     * @param callable $onReturn
     * @param callable|null $onResume
     * @return void
     * @throws Throwable
     */
    public static function waitAll(array $fibers, $onReturn, callable $onResume = null): void
    {
        $activeFibers = [];

        foreach ($fibers as $fiber) {
            $fiber->start();
            $activeFibers[] = $fiber;
        }

        while (count($activeFibers) > 0) {
            foreach ($activeFibers as $index => $activeFiber) {
                if ($activeFiber->isSuspended() && $activeFiber->isTerminated() === false) {
                    $suspendValue = $activeFiber->resume();
                    if ($onResume !== null) {
                        $onResume($suspendValue);
                    }
                } elseif ($activeFiber->isTerminated()) {
                    $onReturn($activeFiber->getReturn());
                    unset($activeFibers[$index]);
                }
            }
        }
    }

}
