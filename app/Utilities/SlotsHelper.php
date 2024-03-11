<?php

namespace App\Utilities;

class SlotsHelper
{
    public static function getSlots(): array
    {
        $start = today()->setTime(9, 0, 0);
        $end = today()->setTime(17, 30, 0);
        $slots = array();

        while ($start <= $end) {
            $slot = array();
            for($i = 0; $i < 2; $i++) {
                $slot[] = $start->format('h:i A');
                $start = $start->addMinutes(30);
            }
            $slots[] = $slot;
        }

        return $slots;
    }
}
