<?php namespace App\Http;

use Illuminate\Database\Eloquent\Model;

class JiaYuan extends Model
{
	protected static $unguarded = true;

	public static function saveData($attributes=array())
	{
		$obj = new static;
		foreach ($attributes as $key => $value) {
			$obj->$key = $value;
		}
		$obj->save();
		return static::find($obj->id);
	}
}