<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
	use SoftDeletes;

	protected $table = 'products';
	protected $dates = ['deleted_at'];

	public function skus()
	{
		return $this->hasMany('App\ProductSku', 'product_id');
	}

	public function product_attributes()
	{
		return $this->hasMany('App\ProductAttribute', 'product_id');
	}

	public function images()
	{
		return $this->hasMany('App\ProductImage', 'product_id');
	}

	public function category()
	{
		return $this->belongsTo('App\ProductCategory', 'category_id');
	}

	public function brand()
	{
		return $this->belongsTo('App\Brand', 'brand_id');
	}

	public function designer()
	{
		return $this->belongsTo('App\Designer', 'designer_id');
	}
}
