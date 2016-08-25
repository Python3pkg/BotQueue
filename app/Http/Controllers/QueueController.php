<?php

namespace App\Http\Controllers;

use App\Http\Requests\Queue\CreateRequest;
use App\Http\Requests\Queue\DeleteRequest;
use App\Http\Requests\Queue\EditRequest;
use App\Models\Queue;

class QueueController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }

    public function index()
    {
        $myQueues = Queue::mine()->get();

        return view('queue.index', ['queues' => $myQueues]);
    }

    public function view(Queue $queue)
    {
        return view('queue.view', compact('queue'));
    }

    public function getCreate()
    {
        return view('queue.create');
    }

    public function postCreate(CreateRequest $request)
    {
        $fields = $request->only('name', 'delay');

        $queue = new Queue($fields);
        $queue->user()->associate($request->user());
        $queue->save();

        return redirect()->route('queue', [$queue]);
    }

    public function getEdit(Queue $queue)
    {
        $this->authorize('edit', $queue);

        return view('queue.edit', compact('queue'));
    }

    public function postEdit(EditRequest $request, Queue $queue)
    {
        $fields = $request->only('name', 'delay');
        $queue->update($fields);

        return redirect()->route('queue', [$queue]);
    }

    public function getDelete(Queue $queue)
    {
        $this->authorize('delete', $queue);

        return view('queue.delete', compact('queue'));
    }

    public function postDelete(DeleteRequest $request, Queue $queue)
    {
        $queue->delete();

        return redirect('/queues');
    }
}
