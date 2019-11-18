<?php
require('../vendor/autoload.php');

use Carbon\Carbon;
use CobaltGrid\Aviation\AIRACCalculator;

$date = Carbon::now();
if ($_GET['date']) {
    $date = new Carbon($_GET['date']);
}
?>

    <form>
        Date (in format 19 JUN 2019 10:30):
        <input type='text' name='date'/>
        <input type='submit' value='Submit'/>
    </form>

<?php

echo "'Current' Date: " . $date->format('d M Y H:i') . "</br>";
echo "The current AIRAC is " . AIRACCalculator::currentAiracCycle($date) . ", which became effective at " . AIRACCalculator::dateForCycle(AIRACCalculator::currentAiracCycle($date))->format('d/m/Y') . '</br>';
echo "The next effective AIRAC is " . AIRACCalculator::nextAiracCycle($date) . ", which becomes effective at " . AIRACCalculator::nextAiracDate($date)->format('d/m/Y');