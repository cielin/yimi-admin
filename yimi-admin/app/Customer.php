<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'customers';

    public function orders()
    {
    	return $this->hasMany('App\Order', 'customer_id');
    }

    public function addresses()
    {
        return $this->hasMany('App\CustomerAddress', 'customer_id');
    }
}
