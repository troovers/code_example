<?php

namespace App\Http\Middleware;

use App\Model\Users\User;
use Closure;
use Redirect;

class RoleAccess
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $base_route
     * @param $action
     * @return mixed
     */
	public function handle($request, Closure $next, $base_route, $action)
	{
        if(strpos($base_route, '[') !== false && strpos($base_route, ']') !== false) {
            $base_route = str_replace(['[', ']'], '', $base_route);

            $base_routes = explode('.', $base_route);
        }

	    if(strpos($action, '[') !== false && strpos($action, ']') !== false) {
            $action = str_replace(['[', ']'], '', $action);

            $action = explode('.', $action);
        }

        $condition = isset($base_routes) ? !User::is_allowed_to($action, $base_routes[0]) && !User::is_allowed_to($action, $base_routes[1]) : !User::is_allowed_to($action, $base_route);

        // Check if a user is allowed to perform an action
        if($condition) {
            if(!$request->ajax()) {
                session()->flash('alert-danger', 'U heeft niet voldoende rechten om deze actie uit te voeren');
                return Redirect::back();
            } else {
                return response()->json(['error' => true, 'message' => 'U heeft niet voldoende rechten om deze actie uit te voeren']);
            }
        }
		
		return $next($request);
	}
}