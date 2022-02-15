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
use yii\helpers\Console;

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

        foreach($events as $event){
            $date = DateTimeHelper::toDateTime($event->getFieldValue('nextUpcomingEvent'));
            if($date and $date->format('U') < date('U')){

                $event->setFieldValue('nextUpcomingEvent',NextUp::getInstance()->nextup->saveLatestEvent($event));
                Craft::$app->elements->saveElement($event);

                $updatedCount++;
            }
        }

        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);

        echo $updatedCount." event(s) got updated in ".$execution_time."s\n";

        return true;
    }
}
