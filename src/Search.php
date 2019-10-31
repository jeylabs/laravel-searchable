<?php

namespace Spatie\Searchable;

use Illuminate\Support\Arr;
use Illuminate\Foundation\Auth\User;

class Search
{
    protected $aspects = [];

    /**
     * @param string|\Spatie\Searchable\SearchAspect $searchAspect
     *
     * @return \Spatie\Searchable\Search
     */
    public function registerAspect($searchAspect): self
    {
        if (is_string($searchAspect)) {
            $searchAspect = app($searchAspect);
        }

        $this->aspects[$searchAspect->getType()] = $searchAspect;

        return $this;
    }

    public function registerModel(string $modelClass, ...$attributes): self
    {
        if (isset($attributes[0]) && is_callable($attributes[0])) {
            $attributes = $attributes[0];
        }

        if (is_array(Arr::get($attributes, 0))) {
            $attributes = $attributes[0];
        }

        $searchAspect = new ModelSearchAspect($modelClass, $attributes);

        $this->registerAspect($searchAspect);

        return $this;
    }

    public function getSearchAspects(): array
    {
        return $this->aspects;
    }

    public function search(string $query, ?User $user = null, $limit = 10): SearchResultCollection
    {
        return $this->perform($query, $user, $limit);
    }

    public function perform(string $query, ?User $user = null, $limit = 10): SearchResultCollection
    {
        $searchResults = new SearchResultCollection();

        collect($this->getSearchAspects())
            ->each(function (SearchAspect $aspect) use ($query, $user, $searchResults, $limit) {
                $searchResults->addResults($aspect->getType(), $aspect->getResults($query, $user, $limit));
            });

        return $searchResults;
    }
}
