<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    /**
     * Fetch all trending threads.
     *
     * @return array
     */
    public function get()
    {
        return array_map('json_decode', Redis::zrevrange($this->cacheKey(), 0, 3));
    }

    /**
     * Push a new thread to the trending list.
     *
     * @param Thread $thread
     */
    public function push($thread)
    {
        Redis::zincrby($this->cacheKey(), 1, json_encode($thread->id));
    }

    /**
     * Get the cache key name.
     *
     * @return string
     */
    public function cacheKey()
    {
        return app()->environment('testing')
            ? 'testing_trending_threads'
            : 'trending_threads';
    }

    /**
     * Reset all trending threads.
     */
    public function reset()
    {
        Redis::del($this->cacheKey());
    }
}
