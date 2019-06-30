<?php
require('../vendor/autoload.php');

use CobaltGrid\Aviation\AIRACCalculator;
use Carbon\Carbon;

echo "The current AIRAC is " . AIRACCalculator::currentAiracCycle() . ", which became effective at " . AIRACCalculator::dateForCycle(AIRACCalculator::currentAiracCycle());