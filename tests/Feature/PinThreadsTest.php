<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PinThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->refreshApplicationWithLocale('en');
    }

    /** @test */
    function non_administrators_may_not_pin_threads()
    {
        $this->withExceptionHandling();

        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->post(route('pinned-threads.store', $thread))->assertStatus(403);

        $this->assertFalse($thread->fresh()->pinned);
    }

    /** @test */
    public function administrators_can_pin_threads()
    {
        $this->signInAdmin();

        $thread = create(Thread::class);

        $this->post(route('pinned-threads.store', $thread));

        $this->assertTrue($thread->fresh()->pinned, 'Failed asserting that the thread was pinned.');
    }

    /** @test */
    public function administrators_can_unpin_threads()
    {
        $this->signInAdmin();

        $thread = create(Thread::class, ['pinned' => true]);

        $this->delete(route('pinned-threads.destroy', $thread));

        $this->assertFalse($thread->fresh()->pinned, 'Failed asserting that the thread was unlocked.');
    }

    /** @test */
    public function pinned_threads_are_listed_first()
    {
        $this->signInAdmin();

        $threads = create(Thread::class, [], 3);
        $ids = $threads->pluck('id');

        $this->getJson(route('threads'))->assertJson([
            'data' => [
                ['id' => $ids[0]],
                ['id' => $ids[1]],
                ['id' => $ids[2]],
            ]
        ]);

        $this->post(route('pinned-threads.store', $pinned = $threads->last()));

        $this->getJson(route('threads'))->assertJson([
            'data' => [
                ['id' => $pinned->id],
                ['id' => $ids[0]],
                ['id' => $ids[1]],
            ]
        ]);
    }
}
