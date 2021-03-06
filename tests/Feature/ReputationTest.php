<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Reputation;

class ReputationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshApplicationWithLocale('en');
    }

    /** @test */
    public function a_user_gains_points_when_they_create_a_thread()
    {
        $thread = create(Thread::class);

        $this->assertEquals(Reputation::THREAD_WAS_PUBLISHED, $thread->creator->reputation);
    }

    /** @test */
    public function a_user_lose_points_when_they_delete_a_thread()
    {
        $this->signIn();

        $thread = create(Thread::class, ['user_id' => auth()->id()]);

        $this->assertEquals(Reputation::THREAD_WAS_PUBLISHED, $thread->creator->reputation);

        $this->delete($thread->path());

        $this->assertEquals(0, $thread->creator->fresh()->reputation);
    }

    /** @test */
    public function a_user_gains_points_when_they_reply_to_a_thread()
    {
        $thread = create(Thread::class);

        $reply = $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Here is a reply.'
        ]);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);
    }

    /** @test */
    public function a_user_loses_points_when_their_reply_to_a_thread_is_deleted()
    {
        $this->signIn();

        $reply = create(Reply::class, ['user_id' => auth()->id()]);

        $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);

        $this->delete(route('replies.destroy', $reply->id));

        $this->assertEquals(0, $reply->owner->fresh()->reputation);
    }

    /** @test */
    public function a_user_gains_points_when_their_reply_is_marked_as_best()
    {
        $thread = create(Thread::class);

        $thread->markBestReply($reply = $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Here is a reply.'
        ]));

        $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;
        $this->assertEquals($total, $reply->owner->reputation);
    }

    /** @test */
    public function when_a_thread_owner_changes_their_preferred_best_reply_the_points_should_be_transferred()
    {
        // Given we have a current best reply...
        $thread = create(Thread::class);
        $thread->markBestReply($firstReply = $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Here is a reply.'
        ]));

        // The owner of the first reply should now receive the proper reputation...
        $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;
        $this->assertEquals($total, $firstReply->owner->reputation);

        // But, if the owner of the thread decides to choose a different best reply...
        $thread->markBestReply($secondReply = $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Here is a better reply.'
        ]));

        // Then the original recipient of the best reply reputation should be stripped of those points.
        $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED - Reputation::BEST_REPLY_AWARDED;
        $this->assertEquals($total, $firstReply->owner->fresh()->reputation);

        // And those points should now be reflected on the account of the new best reply owner.
        $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;
        $this->assertEquals($total, $secondReply->owner->reputation);
    }

    /** @test */
    public function a_user_gains_points_when_their_reply_is_favorited()
    {
        $this->signIn();

        $thread = create(Thread::class);

        $reply = $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Some reply'
        ]);

        $this->post(route('replies.favorites.store', $reply->id));

        $total = Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED;

        $this->assertEquals($total, $reply->owner->fresh()->reputation);
        $this->assertEquals(0, auth()->user()->reputation);
    }

    /** @test */
    public function a_user_loses_points_when_their_favorited_reply_is_unfavorited()
    {
        $reply = create(Reply::class, ['user_id' => create(User::class)]);

        $this->signIn();

        $this->post(route('replies.favorites.store', $reply->id));

        $total = Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED;
        $this->assertEquals($total, $reply->owner->fresh()->reputation);

        $this->delete(route('replies.favorites.destroy', $reply->id));

        $total = Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED - Reputation::REPLY_FAVORITED;
        $this->assertEquals($total, $reply->owner->fresh()->reputation);
        $this->assertEquals(0, auth()->user()->reputation);
    }
}
