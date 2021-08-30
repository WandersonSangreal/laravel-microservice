<?php


namespace App\Models\Traits;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadFiles
{
    protected abstract function uploadDir();

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

    public static function extractFiles(array &$attributes = [])
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

}
