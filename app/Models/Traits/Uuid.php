<?php


namespace App\Models\Traits;

use \Ramsey\Uuid\Uuid as RamseyUuid;

trait Uuid
{

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = RamseyUuid::uuid4()->toString();
        });
    }

}
