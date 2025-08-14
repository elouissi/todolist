<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index($taskId)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($taskId);
        $comments = $task->comments()->with('user:id,full_name,email')->get();
        
        return response()->json(['data' => $comments]);
    }

    public function store(Request $request, $taskId)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($taskId);
        
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        // Load the user relationship for the response
        $comment->load('user:id,full_name,email');

        return response()->json(['data' => $comment, 'message' => 'Comment added successfully'], 201);
    }

    public function update(Request $request, $taskId, $commentId)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($taskId);
        $comment = $task->comments()->where('user_id', Auth::id())->findOrFail($commentId);
        
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment->update([
            'content' => $request->content,
        ]);

        // Load the user relationship for the response
        $comment->load('user:id,full_name,email');

        return response()->json(['data' => $comment, 'message' => 'Comment updated successfully']);
    }

    public function destroy($taskId, $commentId)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($taskId);
        $comment = $task->comments()->where('user_id', Auth::id())->findOrFail($commentId);
        
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
} 