<?php

namespace App\Model\Users;

use App\Model\Authorization\Role;
use App\Notifications\ResetPassword;
use Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Session;
use App\Model\Users\Roles as User_Roles;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $dates = ['last_login'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'active', 'created_at', 'updated_at'];

    const DISPLAY_NAME = 'IF(users.insertion IS NULL, CONCAT(users.first_name, \' \', users.last_name), CONCAT(users.first_name, \' \', users.insertion, \' \', users.last_name))';


    /**
     * A user has many roles
     *
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany('App\Model\Authorization\Role', 'user_roles');
    }


    /**
     * Check if a user has permission to perform an action
     *
     * @param string|array $action
     * @param array|mixed $base_route
     * @param int $user_id The user we are checking access rights for
     * @return bool
     */
    public static function is_allowed_to($action, $base_route, $user_id = null) {
        if(is_null($user_id)) {
            $user_id = Auth::user()->id;
        }

        if(self::is_super_admin($user_id)) {
            return true;
        }

        $role = Role::where('base_route', $base_route)->first(['active']);

        if(!$role['active']) {
            return false;
        }

        if(is_array($action)) {
            $where = [];

            foreach ($action as $column) {
                $where[] = 'user_roles.' . $column . ' = 1';
            }

            $where = implode(' OR ', $where);
        } elseif($action == '*') {
            $where = [];

            // User is allowed to perform all actions
            $where[] = 'user_roles.view = 1';
            $where[] = 'user_roles.add = 1';
            $where[] = 'user_roles.edit = 1';
            $where[] = 'user_roles.delete = 1';

            $where = implode(' AND ', $where);
        } else {
            $where = 'user_roles.' . $action . ' = 1';
        }

        $roles = User_Roles::where('user_id', $user_id)
            ->leftJoin('roles AS r', function($query) use($base_route) {
                $query->on('user_roles.role_id', '=', 'r.id');
            })
            ->where('r.base_route', $base_route)
            ->whereRaw('(' . $where . ')')
            ->count();

        return $roles > 0 || self::is_admin($user_id);
    }


    /**
     * Check if a user has access to a specific route
     *
     * @param string $base_route The base route
     * @return bool
     */
    public function has_access_to($base_route)
    {
        $roles = $this->roles()->get();

        foreach ($roles as $role) {
            if (lcfirst($role->base_route) == lcfirst($base_route)) {
                return true;
            }
        }
    }


    /**
     * Check if a user is an administrator
     *
     * @param int $user_id The user we are checking access rights for
     * @return bool
     */
    public static function is_admin($user_id = null) {
        if(is_null($user_id)) {
            $user_id = Auth::user()->id;
        } elseif($user_id == 0) {
            return false;
        }

        $roles = User_Roles::where('user_id', $user_id)
            ->where('role_id', 1)
            ->count();

        return $roles > 0;
    }


    /**
     * Check if a user is a super administrator
     *
     * @param int $user_id The user we are checking access rights for
     * @return bool
     */
    public static function is_super_admin($user_id = null) {
        if(is_null($user_id)) {
            return Auth::user()->admin == 1;
        } elseif($user_id == 0) {
            return false;
        }

        $user = User::find($user_id);

        return $user->admin == 1;
    }


    /**
     * Get the full name of a user
     *
     * @return string
     */
    public function full_name() {
        return is_null($this->insertion) || empty($this->insertion) ? $this->first_name . ' ' . $this->last_name : $this->first_name . ' ' . $this->insertion . ' ' . $this->last_name;
    }
}
