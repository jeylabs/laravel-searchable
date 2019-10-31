<?php

namespace Spatie\Searchable;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\User;

abstract class SearchAspect
{
    abstract public function getResults(string $term, User $user = null, $limit = 10): Collection;

    public function getType(): string
    {
        if (isset(static::$searchType)) {
            return static::$searchType;
        }

        $className = class_basename(static::class);

        $type = Str::before($className, 'SearchAspect');

        $type = Str::snake(Str::plural($type));

        return Str::plural($type);
    }
}
