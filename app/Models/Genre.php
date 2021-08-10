<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use Uuid;
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;

    protected $casts = ['id' => 'string', 'is_active' => 'boolean'];
    protected $fillable = ['name', 'is_active'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }

}
