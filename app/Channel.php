<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Channel extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    public $appends = ['threads_count'];

    /**
     * Get the route key name for Laravel.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * A channel consists of threads.
     *
     * @return HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Get tags associated with a channel.
     *
     * @param int $limit
     * @return array
     */
    public function getSimilarTags(int $limit = 10): array
    {
        return $this->threads
            ->map(function ($thread) {
                return $thread->tags->pluck('name');
            })
            ->flatten()
            ->unique()
            ->slice(0, $limit)
            ->toArray();
    }

    /**
     * Add threads count to the model.
     *
     * @return integer
     */
    public function getThreadsCountAttribute(): int
    {
        return $this->threads()->count();
    }

    /**
     * Set the proper name attribute.
     *
     * @param string $name
     */
    public function setNameAttribute($name): void
    {
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = Str::slug($name, '-');
    }
}
