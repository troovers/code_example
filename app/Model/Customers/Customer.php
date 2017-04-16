<?php

namespace App\Model\Customers;

use App;
use Config;
use DB;
use Eloquent;

class Customer extends Eloquent
{
	protected $table = 'customers';

	protected $primaryKey = 'id';


    /**
     * A customer belongs to a license
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function license() {
        return $this->belongsTo('App\Model\Customers\License');
    }


    /**
     * Get the current customer according to the subdomain
     *
     * @return mixed
     */
	public static function get_current_customer() {
        $domain = explode('.', $_SERVER['HTTP_HOST']);

        $customer = DB::connection('main')->select('select * from customers where subdomain = :subdomain', [ 'subdomain' => $domain[0] ]);

	    return $customer[0];
    }


    /**
     * Connect to the customers database
     */
    public function connect_to_database() {
        // Make a switch for the local environment
        if(App::environment('local') || App::environment('dev')) {
            $database_suffix = '_gcm_dev';
        } else {
            $database_suffix = '_gcm_nl';
        }

        Config::set('database.connections.tenant.database', $this->database_name . $database_suffix);

        Config::set('database.default', 'tenant');
        DB::reconnect('tenant');
    }


    /**
     * Get the customers with an active license
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function get_active_customers() {
        return self::with(['license' => function ($query) {
            $query->where('start_date', '<=', date('Y-m-d H:i:s'))
                ->where('end_date', '>=', date('Y-m-d H:i:s'))
                ->where('active', 1);
        }])->get();
    }
}

?>