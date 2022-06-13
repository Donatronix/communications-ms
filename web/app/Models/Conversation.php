<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sumra\SDK\Traits\UuidTrait;

class User extends Model
{
    use HasFactory;
    use UuidTrait;
    use SoftDeletes;

    protected $fillable = [
        'first_user_id', 'second_user_id', 'status',
    ];
}
