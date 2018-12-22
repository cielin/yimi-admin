<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Designer extends Model
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'designers';

    public function products()
    {
    	return $this->hasMany('App\Product', 'designer_id');
	}
	
	public function portfolios()
	{
		return $this->hasMany('App\Portfolio', 'designer_id');
	}
}
