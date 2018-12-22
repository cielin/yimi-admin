<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
	protected $table = 'product_categories';

	public function parent()
	{
		return $this->belongsTo('App\ProductCategory', 'parent_id');
	}

	public function children()
	{
		return $this->hasMany('App\ProductCategory', 'parent_id');
	}

	public function products()
	{
		return $this->hasMany('App\Product', 'category_id');
	}
}
