<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use App\Reply;
use App\Thread;
use App\Http\Requests\CreatePostRequest;

class RepliesController extends Controller
{
    /**
     * Create a new RepliesController instance.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  $channelId
     * @param Thread $thread
     *
     * @return mixed
     */
    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->simplePaginate(5);
    }

    /**
     * Persist a new reply.
     *
     * @param integer $channelId
     * @param Thread $thread
     * @param CreatePostRequest $form
     * @return mixed
     *
     * @throws Exception
     */
    public function store($channelId, Thread $thread, CreatePostRequest $form)
    {
        if ($thread->locked) {
            return response('Thread is locked', 422);
        }

        return $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ])->load('owner');
    }

    /**
     * Update an existing reply.
     *
     * @param Reply $reply
     * @throws
     */
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->update(request()->validate(['body' => 'required|spamfree']));
    }

    /**
     * Delete the given reply.
     *
     * @param  Reply $reply
     * @return RedirectResponse
     *
     * @throws
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted!']);
        }

        return back();
    }
}
