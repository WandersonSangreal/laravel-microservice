<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\Uuid;

    protected $casts = ['id' => 'string'];
    protected $fillable = ['name', 'description', 'is_active'];

}
