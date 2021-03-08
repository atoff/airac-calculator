<?php

use Carbon\Carbon;
use CobaltGrid\Aviation\AIRACCalculator;
use PHPUnit\Framework\TestCase;

final class NextAiracDateTest extends TestCase
{
    public function testItCanFindNextAiracDate(): void
    {
        // 1906 cycle effective at 23 MAY 19
        // 1907 cycle effective at 20 JUN 19
        Carbon::setTestNow(new Carbon('23 MAY 19'));

        $this->assertEquals(AIRACCalculator::nextAiracDate(), new Carbon('20 JUN 19'));
        $this->assertEquals(AIRACCalculator::nextAiracDate(new Carbon('20 JUN 19')), new Carbon('18 JUL 19'));
        $this->assertEquals(AIRACCalculator::nextAiracDate(new Carbon('21 JUN 19')), new Carbon('18 JUL 19'));
    }

    public function testItCanFindNextAiracDateForExceptionalYear(): void
    {
        // 2013 cycle effective at 03 DEC 20
        // 2014 cycle effective at 31 DEC 20
        // 2101 cycle effective at 28 JAN 21
        $this->assertEquals(AIRACCalculator::nextAiracDate(new Carbon('02 DEC 20')), new Carbon('03 DEC 20'));
        $this->assertEquals(AIRACCalculator::nextAiracDate(new Carbon('03 DEC 20')), new Carbon('31 DEC 20'));
        $this->assertEquals(AIRACCalculator::nextAiracDate(new Carbon('31 DEC 20')), new Carbon('28 JAN 21'));
    }
}
