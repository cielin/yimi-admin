<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistories extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order_status_histories';

    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id');
    }
}
