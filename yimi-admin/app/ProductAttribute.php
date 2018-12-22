<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $table = 'product_attributes';

    public function product()
    {
    	return $this->belongsTo('App\Product', 'product_id');
    }

    public function product_sku()
    {
    	return $this->belongsTo('App\ProductSku', 'product_sku_id');
    }

    public function product_attr_key()
    {
    	return $this->belongsTo('App\ProductAttrKey', 'product_attr_key_id');
    }

    public function product_attr_value()
    {
    	return $this->belongsTo('App\ProductAttrValue', 'product_attr_value_id');
    }
}
