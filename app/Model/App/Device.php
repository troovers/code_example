<?php

namespace App\Model\App;

use Auth;
use Eloquent;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Session;
use App\Model\Users\Roles as User_Roles;

class Device extends Eloquent
{
    use Notifiable;

    protected $table = 'app_devices';

    protected $primaryKey = 'id';

    protected $dates = ['last_active'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * Editors belong to a newsletter
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('App\Model\Users\User');
    }
}
