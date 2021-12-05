<?php

namespace Yamadashy\TryFiber;

class FiberUtil
{

    public static array $activeAwaits = [];

    /**
     * @param \Fiber $childFiber
     * @return mixed
     * @throws \Throwable
     */
    public static function wait(\Fiber $fiber, $onResume = null): mixed
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
     * @param \Fiber[] $fibers
     * @param callable $onReturn
     * @param callable $onResume
     * @return mixed
     * @throws \Throwable
     */
    public static function waitAll(array $fibers, $onReturn, $onResume = null): mixed
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

        return '';
    }

    /**
     * @return void
     */
    public static function awaitCurrents(): void
    {

        while (count(self::$activeAwaits) > 0) {
            $toRemove = [];
            foreach (self::$activeAwaits as $index => $pair) {
                $parentFiber = $pair[0];
                $childFiber = $pair[1];

                if ($parentFiber->isSuspended() && $parentFiber->isTerminated() === false) {
                    // Resume the parent fiber
                    $parentFiber->resume();
                } elseif ($parentFiber->isTerminated()) {
                    // Register this fiber index to be removed from the activeAwaits
                    $toRemove[] = $index;
                }
            }

            foreach ($toRemove as $indexToRemove) {
                unset(self::$activeAwaits[$indexToRemove]);
            }

            // Re-index the array
            self::$activeAwaits = array_values(self::$activeAwaits);
        }
    }
}
