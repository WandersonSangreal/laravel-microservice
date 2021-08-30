<?php

namespace App\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Video extends Model
{
    use Uuid;
    use HasFactory;
    use SoftDeletes;
    use UploadFiles;

    const RATING_LIST = ['L', '10', '12', '14', '16', '18'];

    public $incrementing = false;

    protected $fillable = ['title', 'description', 'year_launched', 'opened', 'rating', 'duration'];

    protected $casts = ['id' => 'string', 'opened' => 'boolean', 'year_launched' => 'integer', 'duration' => 'integer'];

    public static $fileFields = ['video_file'];

    public static function create(array $attributes = []): Model|Builder
    {
        $files = self::extractFiles($attributes);

        try {

            DB::beginTransaction();

            /**
             * @var Video $creation
             */
            $creation = static::query()->create($attributes);
            static::handleRelations($creation, $attributes);

            $creation->uploadFiles($files);

            DB::commit();

            return $creation;

        } catch (Exception $exception) {

            if (isset($creation)) {
                $creation->deleteFiles($files);
            }

            DB::rollBack();

            throw $exception;

        }
    }

    public function update(array $attributes = [], array $options = [])
    {
        $files = self::extractFiles($attributes);

        try {

            DB::beginTransaction();

            $updated = parent::update($attributes, $options);
            static::handleRelations($this, $attributes);

            if ($updated) {
                # todo upload and delete old files
            }

            DB::commit();

            return $updated;

        } catch (Exception $exception) {

            # todo delete file

            DB::rollBack();

            throw $exception;

        }

    }

    public static function handleRelations($video, array $attributes)
    {
        if (array_key_exists('categories_id', $attributes)) {
            $video->categories()->sync($attributes['categories_id']);
        }

        if (array_key_exists('genres_id', $attributes)) {
            $video->genres()->sync($attributes['genres_id']);
        }
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class)->withTrashed();
    }

    protected function uploadDir()
    {
        return $this->id;
    }
}
