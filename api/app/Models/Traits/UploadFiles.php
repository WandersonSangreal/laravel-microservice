<?php


namespace App\Models\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

trait UploadFiles
{
    public array $oldFiles = [];

    protected abstract function uploadDir();

    public static function bootUploadFiles()
    {
        static::updating(function (Model $model) {

            $updatedFiels = array_keys($model->getDirty()); # o que foi modificado
            $updatedFiles = array_intersect($updatedFiels, self::$fileFields);

            $files = Arr::where($updatedFiles, function ($file) use ($model) {
                return $model->getOriginal($file); # valor antigo, antes do update
            });

            $model->oldFiles = array_map(function ($file) use ($model) {
                return $model->getOriginal($file); # valor antigo, antes do update
            }, $files);

        });
    }

    /**
     * @param UploadedFile[] $files
     */
    public function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }

    /**
     * @param UploadedFile $file
     */
    public function uploadFile(UploadedFile $file)
    {
        # Storage::putFile($this->uploadDir(), $file);

        $file->store($this->uploadDir());
    }

    public function deleteOldFiles()
    {
        $this->deleteFiles($this->oldFiles);
    }

    public function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }

    /**
     * @param string|UploadedFile $file
     */
    public function deleteFile(string|UploadedFile $file)
    {
        $filename = $file instanceof UploadedFile ? $file->hashName() : $file;

        Storage::delete("{$this->uploadDir()}/{$filename}");

    }

    public static function extractFiles(array &$attributes = []): array
    {
        $files = [];

        foreach (self::$fileFields as $file) {
            if (isset($attributes[$file]) && $attributes[$file] instanceof UploadedFile) {
                $files[] = $attributes[$file];
                $attributes[$file] = $attributes[$file]->hashName();
            }
        }

        return $files;
    }

    protected function createUrl($filename): ?string
    {
        if (!$filename) {
            return null;
        }

        $path = "{$this->uploadDir()}/{$filename}";

        if (Storage::exists($path)) {
            return Storage::url($path);
        }

        return null;
    }

}
