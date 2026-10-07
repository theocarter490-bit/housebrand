<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskCommentStoreRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\TaskComment;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    use FileUploadTrait;

    public function storeComment(TaskCommentStoreRequest $request)
    {
        try {
            $taskComment = new TaskComment();
            $taskComment->task_id = $request->taskId;
            $taskComment->user_id = \Auth::user()->id;
            $taskComment->comment = $request->comment;
            if ($request->hasFile('file')){
                $taskComment->file = $this->uploadFile($request->file('file'), 'projects/tasks/'.$request->taskId.'/comments');
            }
            $taskComment->save();
            return response()->json(['message'=> 'Comment added successfully', 'status'=> 200, 'comment'=> $request->comment]);
        }catch (\Exception $e){
            return response()->json(['message'=> 'Something went wrong!', 'status'=> 500]);
        }
    }
}
