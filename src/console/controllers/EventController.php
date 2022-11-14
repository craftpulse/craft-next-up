<?php
/**
 * Next Up plugin for Craft CMS 3.x
 *
 * Get the next upcoming event date from the matrix
 *
 * @link      https://percipio.london/
 * @copyright Copyright (c) 2022 Percipio.london
 */

namespace percipiolondon\nextup\console\controllers;

use craft\helpers\DateTimeHelper;
use percipiolondon\nextup\NextUp;

use Craft;
use yii\console\Controller;

/**
 * Default Command
 *
 * @author    Percipio.london
 * @package   NextUp
 * @since     1.0.0
 */
class EventController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle next-up/default console commands
     *
     * @return mixed
     */
    public function actionSaveUpcoming()
    {
        $time_start = microtime(true);

        $events = \craft\elements\Entry::find()
            ->section('events')
            ->site('*')
            ->anyStatus()
            ->all();

        $updatedCount = 0;

        echo "Start fetching upcoming events" . PHP_EOL;

        foreach($events as $event){
            $date = DateTimeHelper::toDateTime($event->getFieldValue('nextUpcomingEvent'));
            $startCheckingFrom = new \DateTime();

            //check if next upcoming event is older than yesterday but younger than 1 year ago
            if ($date and $date->format('U') < $startCheckingFrom->modify('+ 1 day')->format('U') and $date->format('U') > $startCheckingFrom->modify('- 1 year')->format('U')){

                $latestDate = NextUp::getInstance()->nextup->saveLatestEvent($event, true);

                if (is_null($latestDate)) {
                    $latestDate = NextUp::getInstance()->nextup->saveLatestEvent($event);
                }

                echo "Update event " .$event->title .": " .$date->format('Y-m-d') . ' ' . $date->format('H:i:s'). " to ". $latestDate . PHP_EOL;

                $event->setFieldValue('nextUpcomingEvent',$latestDate);
                Craft::$app->elements->saveElement($event);

                $updatedCount++;
            }
        }

        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);

        echo $updatedCount." event(s) got updated in ".$execution_time."s" . PHP_EOL;

        return true;
    }
}
