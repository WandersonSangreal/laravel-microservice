<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class VideoSeeder extends Seeder
{
    private $genres;
    private $relations = [
        'genres_id' => [],
        'categories_id' => []
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dir = Storage::getDriver()->getAdapter()->getPathPrefix();
        File::deleteDirectory($dir, true);

        $self = $this;

        $this->genres = Genre::all();

        Model::reguard();

        Video::factory(100)->make()->each(function (Video $video) use ($self) {

            $self->fetchRelations();

            $files = [
                'thumb_file' => $self->getImageFile(),
                'banner_file' => $self->getImageFile(),
                'trailer_file' => $self->getVideoFile(),
                'video_file' => $self->getVideoFile()
            ];

            Video::create(array_merge($video->toArray(), $files, $this->relations));

        });

        Model::unguard();
    }

    public function fetchRelations()
    {
        $categoriesID = [];
        $subGenres = $this->genres->random(5)->load('categories');

        foreach ($subGenres as $genre) {
            array_push($categoriesID, ...$genre->categories->pluck('id')->toArray());
        }

        $categoriesID = array_unique($categoriesID);
        $genresID = $subGenres->pluck('id')->toArray();

        $this->relations['genres_id'] = $genresID;
        $this->relations['categories_id'] = $categoriesID;

    }

    public function getImageFile(): UploadedFile
    {
        return new UploadedFile(storage_path('faker/thumbs/image.jpg'), 'image.jpg');
    }

    public function getVideoFile(): UploadedFile
    {
        return new UploadedFile(storage_path('faker/videos/video.mp4'), 'video.mp4');
    }

}
