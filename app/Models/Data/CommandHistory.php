<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model AS Model;
use App\Traits\BaseTrait;

class CommandHistory extends Model
{
    use BaseTrait;
    protected $table = 'xcar_data.command_histories';
    protected $fillable = ['*'];
    public $timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'parame'      => 'object',
    ];
}
