<?php

namespace percipiolondon\nextup\services;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use yii\base\Component;

class NextUpService extends Component
{
    public function saveLatestEvent($entry, $checkOnlyFutureDates = false)
    {
        switch($entry->type->handle) {
            case 'locationEvent':
                $eventDates = $entry->eventDatesTime;
                break;
            case 'hybridEvent':
                $eventDates = $entry->eventHybridDatesTime;
                break;
            default:
                $eventDates = $entry->eventDatesTimeOnline;
        }

        $eventDays = $this->_sortEventDates($eventDates->all(), $checkOnlyFutureDates);

        if(sizeOf($eventDays) === 0){
            //if no existing date, at least save the lasted occured
            $eventDays = $this->_sortEventDates($eventDates->all(), false);
            return Db::prepareDateForDb(DateTimeHelper::toDateTime(end($eventDays)));
        }

        return Db::prepareDateForDb(DateTimeHelper::toDateTime($eventDays[0]));
    }

    private function _sortEventDates($dates, $checkOnlyFutureDates) {

        $eventDays = [];

        foreach( $dates as $date ) {

            if($date->startDateTime && $date->startTime){

                $eventDate = strtotime($date->startDateTime->format('Y-m-d') . ' ' . $date->startTime->format('H:i:s')) ?? null;

                if ($checkOnlyFutureDates) {
                    if ( $eventDate > date('U') ) {
                        $eventDays[] = $eventDate;
                    }
                } else {
                    $eventDays[] = $eventDate;
                }

            }
        }

        usort($eventDays, function($a, $b) {
            return ($a < $b) ? -1 : 1;
        });

        return $eventDays;
    }
}