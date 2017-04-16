<?php

namespace App\Model\Helpers;

use Carbon\Carbon;

class Date
{
    const DATE_LONG = '%d-%m-%Y %H:%M';

    const DATE_SHORT = '%d-%m-%Y';

    /**
     * Get a timestamp of a specific date and time
     *
     * @param $date
     * @param $time
     * @return false|int
     */
    public static function timestamp($date, $time) {
        // Set the time
        if (strpos($time, ':') === false) {
            $hour   = $time;
            $minute = 0;
            $second = 0;
        } else {
            $time   = explode(':', $time);
            $hour   = $time[0];
            $minute = ( isset( $time[1] ) ? $time[1] : 0 );
            $second = ( isset( $time[2] ) ? $time[2] : 0 );
        }

        // See if there's a leading zero in the hour
        if (substr($hour, 0, 1) == '0' && strlen($hour) == 2) {
            $hour = substr($hour, 1);
        }
        if (substr($minute, 0, 1) == '0' && strlen($minute) == 2) {
            $minute = substr($minute, 1);
        }
        if (substr($second, 0, 1) == '0' && strlen($second) == 2) {
            $second = substr($second, 1);
        }

        if ($hour >= 24) {
            return strtotime($date . ' ' . ( $hour - 24 ) . ':' . $minute . ':' . $second) + ( 24 * 3600 );
        } else {
            return strtotime($date . ' ' . $hour . ':' . $minute . ':' . $second);
        }
    }


    /**
     * Parse a date as a local date
     *
     * @param $date
     * @param string $format
     * @return string
     */
    public static function format($date, $format = self::DATE_LONG)
    {
        $date = Carbon::parse($date)->timezone('Europe/Amsterdam');

        $date = $date->formatLocalized($format);

        return $date;
    }


    /**
     * Parse a month number to a string
     *
     * @param $month_number
     * @return string
     */
    public static function month_name($month_number)
    {
        // Format the month
        $month = $month_number < 10 ? '0' . $month_number : $month_number;

        // Format the date
        $date  = date('Y') . '-' . $month . '-01';

        // Get the parsed date
        $date = Carbon::parse($date, 'Europe/Amsterdam')->formatLocalized('%B');

        return $date;
    }


    /**
     * Get the amount of years, months and days
     *
     * @param $total_days
     * @return string
     */
    public static function days_to_string($total_days) {
        // Get the number of years
        $years = floor($total_days / 365.25);

        // Get the days remaining
        $total_days = $total_days - ($years * 365.25);

        // Get the months
        $months = floor($total_days / 30.5);

        // Get the remaining days
        $total_days = floor($total_days - $months * 30.5);

        if($years > 0) {
            return $years . ' jaar, ' . $months . ' ' . ($months > 1 || $months == 0 ? 'maanden' : 'maand') . ' en ' . $total_days . ' ' . ($total_days > 1 ? 'dagen' : 'dag');
        } else {
            if($months > 0) {
                return $months . ' ' . ($months > 1 || $months == 0 ? 'maanden' : 'maand') . ' en ' . $total_days . ' ' . ($total_days > 1 ? 'dagen' : 'dag');
            } else {
                return $total_days . ' ' . ($total_days > 1 ? 'dagen' : 'dag');
            }
        }
    }


    /**
     * Get a list of months, with index as month number
     *
     * @return array
     */
    public static function get_months() {
        return [
            1 => 'januari',
            2 => 'februari',
            3 => 'maart',
            4 => 'april',
            5 => 'mei',
            6 => 'juni',
            7 => 'juli',
            8 => 'augustus',
            9 => 'september',
            10 => 'oktober',
            11 => 'november',
            12 => 'december',
        ];
    }


    /**
     * Get the Dutch month name
     *
     * @param $month_number
     * @return mixed
     */
    public static function get_month_name($month_number) {
        $months = self::get_months();

        return $months[$month_number];
    }


    /**
     * Get the age of someone by their date of birth
     *
     * @param string $date_of_birth
     * @param null $today
     * @return int
     */
    public static function get_age($date_of_birth, $today = null) {
        $date_of_birth = new \DateTime($date_of_birth);
        $today         = is_null($today) ? new \DateTime('today') : new \DateTime($today);
        $age           = $date_of_birth->diff($today)->y;

        return $age;
    }
}