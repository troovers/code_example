<?php

namespace App\Http\Middleware;

use App\Model\Users\User;
use Closure;
use Redirect;

class AdminAccess
{

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
	    if(User::is_super_admin()) {
	        return $next($request);
        }

	    if(!User::is_admin()) {
			session()->flash('alert-danger', 'U heeft niet voldoende rechten om deze actie uit te voeren');
			return Redirect::back();
		}
		
		return $next($request);
	}
}