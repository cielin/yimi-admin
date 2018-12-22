<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerComment extends Model
{
    protected $table = 'customer_reviews';

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id');
    }
}
