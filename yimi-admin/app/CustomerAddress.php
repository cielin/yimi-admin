<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $table = 'customer_addresses';

    public function customer()
    {
    	return $this->belongsTo('App\Customer', 'customer_id');
    }
}
