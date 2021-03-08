<?php

use Carbon\Carbon;
use CobaltGrid\Aviation\AIRACCalculator;
use PHPUnit\Framework\TestCase;

final class NextAiracCycleTest extends TestCase
{
    public function testItCanFindNextAiracCycle(): void
    {
        // 1906 cycle effective at 23 MAY 19
        // 1907 cycle effective at 20 JUN 19
        Carbon::setTestNow(new Carbon('23 MAY 19'));

        $this->assertEquals(AIRACCalculator::nextAiracCycle(), '1907');
        $this->assertEquals(AIRACCalculator::nextAiracCycle(new Carbon('20 JUN 19')), '1908');
        $this->assertEquals(AIRACCalculator::nextAiracCycle(new Carbon('21 JUN 19')), '1908');
    }

    public function testItCanFindNextAiracCycleForExceptionalYear(): void
    {
        // 2013 cycle effective at 03 DEC 20
        // 2014 cycle effective at 31 DEC 20
        // 2101 cycle effective at 28 JAN 21
        $this->assertEquals(AIRACCalculator::nextAiracCycle(new Carbon('02 DEC 20')), '2013');
        $this->assertEquals(AIRACCalculator::nextAiracCycle(new Carbon('03 DEC 20')), '2014');
        $this->assertEquals(AIRACCalculator::nextAiracCycle(new Carbon('31 DEC 20')), '2101');
    }
}
