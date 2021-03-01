
# AIRAC Calculator
[![Tests](https://github.com/atoff/airac-calculator/actions/workflows/test.yml/badge.svg)](https://github.com/atoff/airac-calculator/actions/workflows/test.yml)
## About

#### Description
airac-calculator is a lightweight PHP library that provides information on AIRAC cycle effective dates. AIRAC stands for Aeronautical Information Regulation And Control, and the cycles associated with them are used to almost version aviation data. A cycle is valid for 28 days, and so there are usually 13 cycles per year.

Typically, AIRAC cycles are used to update various aviation procedures, including aeronautical charts, flight procedures (such as SIDs and STARS) and other operational data.

The cycle code is created using the two number representation of the year (i.e. 2019 = 19) along with the cycle number (i.e. 01 if the first cycle). Thus, a typical cycle code may look like 1901.

#### Requirements
* PHP 7.2 and above

#### Author
This package was created by [Alex Toff](https://alextoff.uk)

#### License
airac-calculator is licensed under the MIT license, which can be found in the root of the package in the `LICENSE` file.

## Installation

The easiest way to install is through the use of composer:
```
$ composer require cobaltgrid/airac-calculator
```

## Usage
If you are using Composer:
```
<?php

// Include the composer autoloader
require('../vendor/autoload.php');

// Import the class
use CobaltGrid\Aviation\AIRACCalculator;

...

// Example: echo the current cycle
echo AIRACCalculator::currentAiracCycle();
```

## Examples
For examples, see the `examples` folder in the base.

## Testing
The majority of the code base is tested using PHPUnit. To run the suite:
`$ ./phpunit`

## Documentation
All of the publicly accessible functions are detailed below. Note that they are **all static methods**.

### dateForCycle($cycle)
Computes and returns the effective date for a given cycle.
* Arguments:
	* `$cycle` `string/int` The AIRAC cycle code (e.g. 1901)
* Returns:
	* `Carbon\Carbon` Carbon date instance
```
echo AIRACCalculator::dateForCycle('1901')->format('d/m/Y');
// ... 03/01/2019
```

### nextAiracCycle(Carbon $date = null)
Computes and returns the upcoming AIRAC cycle
* Arguments:
	* (opt) `$date` `Carbon\Carbon` A Carbon date instance. If supplied, the resulting cycle will be the next effective cycle from this date, rather than the current date
* Returns:
	* `string` AIRAC Cycle Code
```
// Today is 30/06/2019
echo AIRACCalculator::nextAiracCycle();
// ... 1908
```

### nextAiracDate(Carbon $date = null)
Computes and returns the effective date of the upcoming AIRAC cycle
* Arguments:
	* (opt) `$date` `Carbon\Carbon` A Carbon date instance. If supplied, the resulting date will be the next effective cycle date from this date, rather than from the current date
* Returns:
	* `Carbon\Carbon` Next effective cycle date
```
// Today is 30/06/2019
echo AIRACCalculator::nextAiracDate()->format('d/m/Y');
// ... 18/07/2019
```

### currentAiracCycle(Carbon $date = null)
Computes and returns the current AIRAC cycle code
* Arguments:
	* (opt) `$date` `Carbon\Carbon` A Carbon date instance. If supplied, the resulting cycle will be the effective cycle at this date.
* Returns:
	* `string` AIRAC Cycle Code
```
// Today is 30/06/2019
echo AIRACCalculator::currentAiracCycle();
// ... 1907
```

### isNewAiracDay(Carbon $date = null)
Returns whether an AIRAC effective date lies on this day
* Arguments:
	* (opt) `$date` `Carbon\Carbon` A Carbon date instance. If supplied, instead of using the current day, it will check if the date supplied lies on an update day.
* Returns:
	* `boolean`
```
// Today is 30/06/2019
echo AIRACCalculator::isNewAiracDay();
// ... 0

// Today is 20/06/2019
echo AIRACCalculator::isNewAiracDay();
// ... 1
```

### cyclesForYear($year = null)
Computes and returns a 2D array of the cycles for a year
* Arguments:
	* (opt) `$year` `string|int` A 4 digit year, either in int or string format. If not supplied, will use current year.
* Returns:
	* `array` An array of cycles. Each "cycle" item is an array following a `[$cycleCode, $effectiveDate]` format. The `$effectiveDate` is given as a `Carbon\Carbon` object.
```
foreach (AIRACCalculator::cyclesForYear() as $cycle){
	echo $cycle[0] . ' at ' . $cycle[1]->format('d/m/Y');
}

// 1901 at 03/01/2019
// 1902 at 31/01/2019
// 1903 at 28/02/2019
...
```
