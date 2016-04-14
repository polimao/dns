<?php namespace App\Http;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BaseTrait;

class LaGou extends Model
{
    use BaseTrait;
    protected $casts = [
        'companyLabelList'      =>'array'
    ];
}