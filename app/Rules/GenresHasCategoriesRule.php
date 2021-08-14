<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class GenresHasCategoriesRule implements Rule
{
    private $genresID;
    private $categoriesID;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $categoriesID)
    {
        $this->categoriesID = array_unique($categoriesID);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = is_array($value) ? $value : [];

        $this->genresID = array_unique($value);

        if (!sizeof($this->genresID) || !sizeof($this->categoriesID))
            return false;

        $categories = [];

        foreach ($this->genresID as $genreID) {

            $rows = $this->getRows($genreID);

            if (!$rows->count())
                return false;

            array_push($categories, ...$rows->pluck('category_id')->toArray());

        }

        if (sizeof(array_unique($categories)) !== sizeof($this->categoriesID)) {
            return false;
        }

        return true;

    }

    protected function getRows($genreID): Collection
    {
        return DB::table('category_genre')->where('genre_id', $genreID)->whereIn('category_id', $this->categoriesID)->get();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'A genre ID must be related at least a category ID.';
    }
}
