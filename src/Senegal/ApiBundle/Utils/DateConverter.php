<?php

namespace Pfd\ApiBundle\Utils;

final class DateConverter
{
    public static function convertDateToDatetime($date)
    {
        if ($date instanceof \DateTime) {
            return $date;
        }

        $date = stripslashes($date);

        if (3 == count(explode("/", $date))) {
            $dateParts = explode("/", $date);
            $time = mktime(0, 0, 0, $dateParts[1], $dateParts[0], $dateParts[2]);
            $datetime = new \DateTime();
            $datetime->setTimestamp($time);

            return $datetime;
        } elseif (3 == count(explode("-", $date))) {
            $dateParts = explode("-", $date);
            $time = mktime(0, 0, 0, $dateParts[1], $dateParts[2], $dateParts[0]);
            $datetime = new \DateTime();
            $datetime->setTimestamp($time);

            return $datetime;
        } else {
            return;
        }
    }
}
