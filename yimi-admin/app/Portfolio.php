<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $table = 'portfolios';

    public function designer()
    {
        $this->belongsTo('App\Designer', 'designer_id');
    }
}
