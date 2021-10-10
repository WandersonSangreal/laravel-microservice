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
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    use Uuid;
    use HasFactory;
    use SoftDeletes;
    use UploadFiles;

    const RATING_LIST = ['L', '10', '12', '14', '16', '18'];

    public $incrementing = false;

    protected $fillable = ['title', 'description', 'year_launched', 'opened', 'rating', 'duration', 'video_file', 'thumb_file', 'banner_file', 'trailer_file'];

    protected $appends = ['video_file_url', 'thumb_file_url', 'banner_file_url', 'trailer_file_url'];

    protected $casts = ['id' => 'string', 'opened' => 'boolean', 'year_launched' => 'integer', 'duration' => 'integer'];

    public static $fileFields = ['video_file', 'thumb_file', 'banner_file', 'trailer_file'];

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
                $this->uploadFiles($files);
            }

            DB::commit();

            if ($updated && count($files)) {
                $this->deleteOldFiles();
            }

            return $updated;

        } catch (Exception $exception) {

            if (count($files)) {

                $this->deleteFiles($files);

            }

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

    public function createUrl($field): ?string
    {
        $path = "{$this->id}/{$field}";

        if (Storage::exists($path)) {
            return Storage::url($path);
        }

        return null;
    }

    public function getVideoFileUrlAttribute(): ?string
    {
        return $this->createUrl($this->video_file);
    }

    public function getThumbFileUrlAttribute(): ?string
    {
        return $this->createUrl($this->thumb_file);
    }

    public function getBannerFileUrlAttribute(): ?string
    {
        return $this->createUrl($this->banner_file);
    }

    public function getTrailerFileUrlAttribute(): ?string
    {
        return $this->createUrl($this->trailer_file);
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
