<?php

namespace Yamadashy\TryFiber;


use PHPUnit\Framework\TestCase;

class FiberSampleTest extends TestCase
{

    public function testRun()
    {
        FiberSample::run();

        $this->assertTrue(true);
    }

}
