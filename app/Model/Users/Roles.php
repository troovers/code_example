<?php

namespace App\Model\Users;


use Eloquent;

class Roles extends Eloquent
{
    protected $table = 'user_roles';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public $timestamps = false;

    // The actions a user can perform
    const VIEW = 'view';

    const ADD = 'add';

    const EDIT = 'edit';

    const DELETE = 'delete';


    /**
     * The user roles object belongs to a role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo('App\Model\Authorization\Role');
    }


    /**
     * The user roles object belongs to a role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Model\Users\User');
    }
}
