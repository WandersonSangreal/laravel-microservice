<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genres = Genre::all();

        Video::factory(100)->create()->each(function (Video $video) use ($genres) {
            $categoriesID = [];
            $subGenres = $genres->random(5)->load('categories');
            foreach ($subGenres as $genre) {
                array_push($categoriesID, ...$genre->categories->pluck('id')->toArray());
            }
            $categoriesID = array_unique($categoriesID);
            $video->categories()->attach($categoriesID);
            $video->genres()->attach($subGenres->pluck('id')->toArray());
        });
    }
}
