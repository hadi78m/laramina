<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;

class PlainItem extends Model
{
    protected $table = 'plain_items';

    protected $fillable = [
        'title',
        'description',
    ];
}
