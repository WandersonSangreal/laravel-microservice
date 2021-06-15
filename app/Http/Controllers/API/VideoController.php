<?php

namespace App\Http\Controllers\API;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends ResourceAbstractController
{

    protected function model(): string
    {
        return Video::class;
    }

    protected function rulesStore()
    {
        // TODO: Implement rulesStore() method.
    }

    protected function rulesUpdate()
    {
        // TODO: Implement rulesUpdate() method.
    }
}
