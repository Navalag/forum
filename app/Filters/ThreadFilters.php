<?php

namespace App\Filters;

use App\User;
use Illuminate\Database\Eloquent\Builder;

class ThreadFilters extends Filters
{
    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = ['by', 'popular', 'unanswered', 'tag'];

    /**
     * Filter the query by a given username.
     *
     * @param  string $username
     * @return Builder
     */
    protected function by($username): Builder
    {
        $user = User::where('name', $username)->firstOrFail();

        return $this->builder->where('user_id', $user->id);
    }

    /**
     * Filter the query according to most popular threads.
     *
     * @return Builder
     */
    protected function popular(): Builder
    {
        $this->builder->getQuery()->orders = [];

        return $this->builder->orderBy('replies_count', 'desc');
    }

    /**
     * Filter the query according to those that are unanswered.
     *
     * @return Builder
     */
    protected function unanswered(): Builder
    {
        return $this->builder->where('replies_count', 0);
    }

    /**
     * Filter the query by a given tag.
     *
     * @param  string $tag
     * @return Builder
     */
    protected function tag($tag): Builder
    {
        return $this->builder->whereHas('tags', function ($q) use ($tag) {
            $q->where('name', $tag);
        });
    }
}
