<?php

namespace Tests\Unit\Rules;

use App\Rules\GenresHasCategoriesRule;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GenresHasCategoriesRuleUnitTest extends TestCase
{
    public function test_categories_id_field()
    {
        $rule = new GenresHasCategoriesRule([1, 1, 2, 2]);
        $reflectionClass = new ReflectionClass(GenresHasCategoriesRule::class);
        $reflectionProperty = $reflectionClass->getProperty('categoriesID');
        $reflectionProperty->setAccessible(true);

        $categoriesID = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $categoriesID);

    }

    public function test_genres_id_value()
    {
        $rule = $this->createRuleMock([]);
        $rule->shouldReceive('getRows')->withAnyArgs()->andReturn(collect([]));
        $rule->passes('', [1, 1, 2, 2]);

        $reflectionClass = new ReflectionClass(GenresHasCategoriesRule::class);
        $reflectionProperty = $reflectionClass->getProperty('genresID');
        $reflectionProperty->setAccessible(true);

        $genresID = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1, 2], $genresID);

    }

    public function test_passes_returns_false_when_categories_or_genres_is_array_empty()
    {
        $rule = $this->createRuleMock([1]);
        $this->assertFalse($rule->passes('', []));

        $rule = $this->createRuleMock([]);
        $rule->shouldReceive('getRows')->withAnyArgs()->andReturn(collect([]));
        $this->assertFalse($rule->passes('', [1]));
    }

    public function test_passes_returns_false_when_get_rows_id_empty()
    {
        $rule = $this->createRuleMock([1]);
        $rule->shouldReceive('getRows')->withAnyArgs()->andReturn(collect([]));
        $this->assertFalse($rule->passes('', [1]));
    }

    public function test_passes_returns_false_when_has_category_without_genres()
    {
        $rule = $this->createRuleMock([1, 2]);
        $rule->shouldReceive('getRows')->withAnyArgs()->andReturn(collect([['category_id' => 1]]));
        $this->assertFalse($rule->passes('', [1]));
    }

    public function test_passes_id_valid()
    {
        $rule = $this->createRuleMock([1, 2]);
        $rule->shouldReceive('getRows')->withAnyArgs()->andReturn(collect([['category_id' => 1], ['category_id' => 2]]));
        $this->assertTrue($rule->passes('', [1, 2]));
    }

    protected function createRuleMock(array $categoriesID): MockInterface
    {
        return Mockery::mock(GenresHasCategoriesRule::class, [$categoriesID])->makePartial()->shouldAllowMockingProtectedMethods();
    }

}
