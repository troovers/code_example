<?php

namespace App\Model\Customers;

use App;
use Config;
use DB;
use Eloquent;

class License extends Eloquent
{
	protected $table = 'licenses';

	protected $primaryKey = 'id';


    /**
     * A license has many customers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customers() {
        return $this->hasMany('App\Model\Customers\Customer');
    }
}

?>