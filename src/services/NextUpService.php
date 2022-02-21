<?php

namespace percipiolondon\nextup\services;

use Craft;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use yii\base\Component;

class NextUpService extends Component
{
    public function saveLatestEvent($entry)
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

        $eventDays = $this->_sortEventDates($eventDates->all());

        if(sizeOf($eventDays) === 0){
            return null;
        }

        return Db::prepareDateForDb(DateTimeHelper::toDateTime($eventDays[0]));
    }

    private function _sortEventDates($dates) {

        $eventDays = [];

        foreach( $dates as $date ) {

            if($date->startDateTime && $date->startTime){

                $eventDate = strtotime($date->startDateTime->format('Y-m-d') . ' ' . $date->startTime->format('H:i:s')) ?? null;

                if ( $eventDate > date('U') ) {
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