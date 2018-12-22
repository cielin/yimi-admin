<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
	use SoftDeletes;

	protected $dates = ['deleted_at'];

	/**
	 * Constants status
	 */
	const PUBLISHED = "publised";
	const DRAFT = "draft";

    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
    protected $table = 'articles';

    public function user()
    {
    	return $this->belongsTo('App\User', 'user_id');
    }
}
