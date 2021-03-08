<?php

namespace CobaltGrid\Aviation;

use Carbon\Carbon;

class AIRACCalculator
{

    static $datumDate = "1/2/2020";
    static $datumCycle = "2001";

    /*
        Useful, usable methods
    */

    /**
     * Computes and returns the effective date for a given cycle
     *
     * @param string/int $cycle The AIRAC cycle code (e.g. 1901)
     * @return Carbon
     */
    public static function dateForCycle($cycle)
    {
        $yearDiff = (int)self::cycleYear($cycle) - (int)self::cycleYear(self::$datumCycle);

        $datumIssue = (int)substr(self::$datumCycle, -2);
        $issue = (int)substr($cycle, -2);

        $cycleDiff = $issue - $datumIssue;

        $numCycleDiff = 13 * $yearDiff + $cycleDiff;

        // Check for extraordinary 14 cycle years
        $possibleCycles = self::extraordinaryCycles(new Carbon("31 DEC " . self::cycleYear($cycle)));

        foreach ($possibleCycles as $possibleCycle) {
            if ($cycle >= $possibleCycle) {
                $numCycleDiff++;
            }
        }

        return self::datumDate()->addDays($numCycleDiff * 28);
    }

    /**
     * Computes and returns the upcoming AIRAC cycle
     *
     * @param Carbon|null $date Optional date to compute from. If supplied, the resulting cycle will be the next effective cycle from this date.
     * @return string The AIRAC cycle code
     * @throws \Exception
     */
    public static function nextAiracCycle(Carbon $date = null)
    {
        if (!$date) {
            $date = Carbon::now();
        }

        $twoDigitYear = $date->format('y');
        $month = $date->format('m');

        $attempts = 0;

        while (true) {
            $attempts++;


            $guess = $twoDigitYear . $month;
            $guessDate = self::dateForCycle($guess);

            if ($attempts > 10) {
                throw new \Exception('Unable to find next AIRAC!');
            }

            if ($guessDate->greaterThan($date) && $guessDate->diffInDays($date) <= 28) {
                // The guessed cycle is in the future and within 28 days. Yes!
                return $guess;
            }
            // The guessed cycle is before the current date
            if ($month < 13) {
                $month = str_pad($month + 1, 2, '0', STR_PAD_LEFT);
                continue;
            }

            if ($month == 13) {
                // Check to see if is "exceptional year"
                $years = self::extraordinaryCycles();
                if (in_array("20" . $twoDigitYear, $years)) {
                    $month = str_pad($month + 1, 2, '0', STR_PAD_LEFT);
                    continue;
                }
            }

            // Else, roll over year
            $twoDigitYear = $date->copy()->addYear()->format('y');
            $month = "01";
            continue;
        }
    }

    /**
     * Computes and returns the effective date of the upcoming AIRAC cycle
     *
     * @param Carbon|null $date Optional date to compute from. If supplied, the resulting date will be the next effective AIRAC date from this date.
     * @return Carbon
     */
    public static function nextAiracDate(Carbon $date = null)
    {
        if (!$date) {
            $date = Carbon::now();
        }

        return self::dateForCycle(self::nextAiracCycle($date));
    }

    /**
     * Computes and returns the current AIRAC cycle code
     *
     * @param Carbon|null $date Optional date to compute from. If supplied, the resulting cycle will be the effective cycle at this date.
     * @return string
     * @throws \Exception
     */
    public static function currentAiracCycle(Carbon $date = null)
    {
        if (!$date) {
            $date = Carbon::now();
        }
        $twoDigitYear = $date->format('y');
        $month = $date->format('m');

        $attempts = 0;

        while (true) {
            $attempts++;

            $guess = $twoDigitYear . $month;
            $guessDate = self::dateForCycle($guess);
            if ($attempts > 10) {
                throw new \Exception('Unable to find current AIRAC!');
            }

            if ($guessDate == $date) {
                return $guess;
            }

            if ($guessDate->greaterThan($date)) {
                if ($month > 1) {
                    $month = str_pad($month - 1, 2, '0', STR_PAD_LEFT);
                    continue;
                }
                // Else, roll over the year
                $twoDigitYear = (clone $date)->subYear()->format('y');

                // Check to see if is "exceptional year"
                $years = self::extraordinaryCycles();
                if (in_array("20" . $twoDigitYear, $years)) {
                    $month = "14";
                    continue;
                }

                $month = "13";
                continue;
            }

            if ($guessDate->lessThan($date) && $guessDate->diffInDays($date) < 28) {
                return $guess;
            } else {
                // Try increasing by month
                if ($month < 13) {
                    $month = str_pad($month + 1, 2, '0', STR_PAD_LEFT);
                    continue;
                }

                if ($month == 13) {
                    // Check to see if is "exceptional year"
                    $years = self::extraordinaryCycles();
                    if (in_array("20" . $twoDigitYear, $years)) {
                        $month = str_pad($month + 1, 2, '0', STR_PAD_LEFT);
                        continue;
                    }
                }

                // Else, roll over year
                $twoDigitYear = (clone $date)->addYear()->format('y');
                $month = "01";
                continue;
            }
        }
    }

    /**
     * Returns whether an AIRAC effective date lies on this day
     *
     * @param Carbon|null $date Optional date to compute from. If supplied, instead of using the current day, it will check if the date supplied lies on an update day.
     * @return bool
     */
    public static function isNewAiracDay(Carbon $date = null)
    {
        if (!$date) {
            $date = Carbon::now();
        }

        return $date->diffInDays(self::dateForCycle(self::currentAiracCycle($date))) < 1 ? true : false;
    }

    /**
     * Computes and returns a 2D array of the cycles in the year. Items follow a schema of [$cycleCode, $effectiveDate]
     *
     * @param string|int|null $year Optional 4-digit year. If supplied, will return cycles for the given year instead of current year.
     * @return array
     */
    public static function cyclesForYear($year = null)
    {
        if (!$year) {
            $year = Carbon::now()->format('y');
        } else {
            $year = (new Carbon($year . '-01-01'))->format('y');
        }

        // Check if we have an exceptional cycle
        $extra = self::extraordinaryCycles(new Carbon((int)$year + 1), true);

        $numCycles = 13;
        if (in_array($year . '14', $extra)) {
            $numCycles++;
        }

        $cycles = [];

        for ($i = 1; $i <= $numCycles; $i++) {
            $cycleId = $year . str_pad($i, 2, '0', STR_PAD_LEFT);
            $cycles[] = [$cycleId, self::dateForCycle($cycleId)];
        }

        return $cycles;
    }

    /*
        Support functions
    */

    /**
     * Turns two digit year into four digit year
     *
     * @param $cycle Two digit year
     * @return string
     */
    private static function cycleYear($cycle)
    {
        return "20" . substr($cycle, 0, 2);
    }

    /**
     * Computes list of years with 14 cycles instead of 13
     *
     * @param Carbon|null $upToDate Optional date. If supplied, will compute list of years up to this date. If null, 5 'exceptional' years from the current year will be found.
     * @param bool $asCycleNumbers Determines if the returned values should be years (e.g. 2020) or cycles (e.g. 2014)
     * @return array
     */
    private static function extraordinaryCycles(Carbon $upToDate = null, $asCycleNumbers = false)
    {
        // Frequency of 14-cycle years, in years
        $occurrence = "29";

        if ($upToDate) {
            $yearsFromDatum = $upToDate->diffInYears(self::datumDate());
            $nubOccurrences = $yearsFromDatum / $occurrence;
        } else {
            // Take 5 cycles from datum
            $nubOccurrences = 5;
        }

        $list = [];

        for ($i = 0; $i < $nubOccurrences; $i++) {
            if ($asCycleNumbers) {
                $list[] = self::datumDate()->addYears($i * $occurrence)->format('y') . "14";
            } else {
                $list[] = self::datumDate()->addYears($i * $occurrence)->format('Y');
            }
        }

        return $list;
    }

    /**
     * Computes how many extra cycles lie between datum and the requested cycle
     *
     * @param $requestedCycle The 4 digit cycle code to work up to
     * @return int
     */
    private static function numberOfExtraordinaryCycles($requestedCycle)
    {
        $possibleCycles = self::extraordinaryCycles();

        $count = 0;
        foreach ($possibleCycles as $cycle) {
            if ($cycle < $requestedCycle) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Gets the datum date as a Carbon object
     *
     * @return Carbon
     */
    private static function datumDate()
    {
        return new Carbon(self::$datumDate);
    }
}
