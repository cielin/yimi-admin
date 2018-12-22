<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notify extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notify';

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }
}
