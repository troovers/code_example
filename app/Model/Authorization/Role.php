<?php

namespace App\Model\Authorization;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

class Role extends Eloquent
{
	protected $table = 'roles';

	protected $primaryKey = 'id';

	public function users()
	{
		$this->belongsToMany('App\Model\User', 'user_roles', 'role_id', 'user_id');
	}
}

?>