<?php

namespace App\Http\Middleware;

use Closure;
use Config;
use DB;
use Log;
use App\Model\Customers\Customer;

class ConnectToDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $domain = explode('.', $request->getHost());

        $customer = Customer::where('subdomain', $domain[0])->first();

        $database = Config::get('database.default');

        if(!is_null($customer)) {
            if($database == 'main') {
                Config::set('database.connections.tenant.database', $customer->database_name . '_gcm_' . $domain[2]);

                Config::set('database.default', 'tenant');
                DB::reconnect('tenant');
            }
        } else {
            App:abort(403);
        }

        return $next($request);
    }
}
