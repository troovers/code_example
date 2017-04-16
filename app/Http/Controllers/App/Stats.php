<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Model\Helpers\Date;
use App\Model\Helpers\Html;
use DB;
use Illuminate\Http\Request;

class Stats extends BaseController
{
    /** @var String year */
    private $year;

    /** @var String month */
    private $month;


    /**
     * Show a list of member types
     *
     * @param Request $request
     * @return mixed
     */
	public function index(Request $request)
	{
        $this->year  = $request->get('year', date('Y'));
        $this->month = $request->get('month', date('m'));

        // Get the total amount of logins
	    $total     = DB::table('app_logins')->count() / 100;

	    // Select the logins, grouped by weekday
	    $monday    = $total != 0 ? number_format(DB::table('app_logins')->where('weekday', 1)->count() / $total, 1) : 0;
	    $tuesday   = $total != 0 ? number_format(DB::table('app_logins')->where('weekday', 2)->count() / $total, 1) : 0;
	    $wednesday = $total != 0 ? number_format(DB::table('app_logins')->where('weekday', 3)->count() / $total, 1) : 0;
	    $thursday  = $total != 0 ? number_format(DB::table('app_logins')->where('weekday', 4)->count() / $total, 1) : 0;
	    $friday    = $total != 0 ? number_format(DB::table('app_logins')->where('weekday', 5)->count() / $total, 1) : 0;
	    $saturday  = $total != 0 ? number_format(DB::table('app_logins')->where('weekday', 6)->count() / $total, 1) : 0;
	    $sunday    = $total != 0 ? number_format(DB::table('app_logins')->where('weekday', 0)->count() / $total, 1) : 0;

	    $weekly    = [$monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday];

        $month = $this->month < 10 ? '0' . $this->month : $this->month;

        // Get the first day in the selected month
        $start = DB::table('app_users_stats')->selectRaw('DATE_FORMAT(date, \'%e\') AS day')->whereRaw('date LIKE \'' . $this->year . '-' . $month . '-%\'')->orderBy('date', 'ASC')->first();

        // Get the amount of senior and youth app users
        $youth_members  = DB::table('app_users_stats')->select('youth_members')->whereRaw('date LIKE \'' . $this->year . '-' . $month . '-%\'')->orderBy('date', 'ASC')->get();
        $senior_members = DB::table('app_users_stats')->select('senior_members')->whereRaw('date LIKE \'' . $this->year . '-' . $month . '-%\'')->orderBy('date', 'ASC')->get();

        $count_youth = [];
        $count_seniors = [];

        // If the stats do not begin on the first month, insert 0 values till the beginning of the data
        if(!is_null($start)) {
            for($i = 1; $i < $start->day; $i++) {
                $count_youth[] = 0;
                $count_seniors[] = 0;
                $count_formers[] = 0;
            }
        }

        // Add the stats
        foreach($youth_members as $member) {
            $count_youth[] = $member->youth_members;
        }

        // Add the stats
        foreach($senior_members as $member) {
            $count_seniors[] = $member->senior_members;
        }

        // Calculate the amount of days in the selected month
        $days_in_month = [];

        for($days = 1; $days <= cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year); $days++) {
            $days_in_month[] = $days;
        }

        return view('app.stats.index')->with([
            'weekly'         => json_encode($weekly),
            'youth_members'  => json_encode($count_youth),
            'senior_members' => json_encode($count_seniors),
            'form_filters'   => $this->get_form_filters(),
            'days_in_month'  => json_encode($days_in_month),
            'stats_exist'    => DB::table('app_users_stats')->count() > 0
        ]);
	}


    /**
     * Get the form filters for the pages
     *
     * @return array
     */
    public function get_form_filters() {
        return [
            Html::select('year', $this->get_years(), 'year', 'year', $this->year),
            Html::select('month', $this->get_months(), 'month', 'display_name', $this->month)
        ];
    }


    /**
     * Get the years of the stats
     */
    public function get_years() {
        $years = DB::table('app_users_stats')->selectRaw('DISTINCT YEAR(date) AS year')->get();

        return $years;
    }


    /**
     * Get the months of the stats
     */
    public function get_months() {
        $months = DB::table('app_users_stats')->selectRaw('DISTINCT MONTH(date) AS month')->whereRaw('date LIKE \'' . $this->year . '-%\'')->get();

        foreach($months as &$month) {
            $month->display_name = Date::get_month_name($month->month);
        }

        return $months;
    }
}
