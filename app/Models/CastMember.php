<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use Uuid;
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $casts = ['id' => 'string'];
    protected $fillable = ['name', 'type'];
}
