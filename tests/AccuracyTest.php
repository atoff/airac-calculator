<?php

use Carbon\Carbon;
use CobaltGrid\Aviation\AIRACCalculator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

final class AccuracyTest extends TestCase
{
    public function testItWillGiveCorrectDatumDate(): void
    {
        $this->assertEquals(new Carbon(AIRACCalculator::$datumDate), AIRACCalculator::dateForCycle(AIRACCalculator::$datumCycle));
    }

    public function testItIsTheSameAsOfficalSource(): void
    {
        $crawler = new Crawler(file_get_contents('https://www.nm.eurocontrol.int/RAD/common/airac_dates.html'));

        $years = $crawler->filter('table table');
        // Check that it finds 4/5 years as expected
        $this->assertContains(count($years), [4, 5]);

        foreach ($years as $year) {
            $year = new Crawler($year);

            $cycles = $year->filter('tr');

            // Disregard first two rows
            for ($i = 2; $i < count($cycles); $i++) {
                $cycle = $cycles->eq($i);

                $cycleNumber = $cycle->filter('td:nth-of-type(2)')->text();
                $cycleDate = new Carbon($cycle->filter('td:nth-of-type(5)')->text());

                $this->assertEquals($cycleDate, AIRACCalculator::dateForCycle($cycleNumber));
            }
        }
    }

}
