<?php

namespace App\Model\App;

use Auth;
use Eloquent;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Session;
use App\Model\Users\Roles as User_Roles;

class Agenda extends Eloquent
{

    protected $table = 'agenda';

    protected $primaryKey = 'id';

    protected $dates = ['start', 'end'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public $timestamps = false;


    /**
     * Editors belong to a newsletter
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('App\Model\Users\User');
    }
}
