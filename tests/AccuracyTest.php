<?php

use CobaltGrid\AIRACCalculator\AIRACCycle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

final class AccuracyTest extends TestCase
{
    public function testItIsTheSameAsOfficalSource(): void
    {
        $crawler = new Crawler(file_get_contents('https://www.nm.eurocontrol.int/RAD/common/airac_dates.html'));

        $years = $crawler->filter('table table');
        // Check that it finds 3/4/5 years as expected
        $this->assertContains(count($years), [3, 4, 5, 6]);

        foreach ($years as $year) {
            $year = new Crawler($year);

            $cycles = $year->filter('tr');

            // Disregard first two rows
            for ($i = 2; $i < count($cycles); $i++) {
                $cycle = $cycles->eq($i);

                $cycleNumber = $cycle->filter('td:nth-of-type(2)')->text();
                $cycleDateRaw = $cycle->filter('td:nth-of-type(5)')->text();

                preg_match("/\d{2} [A-Z]{3} \d{2,4}/", $cycleDateRaw, $matches);

                $cycleDate = new DateTime($matches[0], new DateTimeZone('UTC'));
                $this->assertEquals($cycleDate, AIRACCycle::fromCycleCode($cycleNumber)->getEffectiveDate(), "Cycle $cycleNumber does not match official source.");
            }
        }
    }
}
