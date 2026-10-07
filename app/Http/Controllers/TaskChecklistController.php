<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskChecklistStoreRequest;
use App\Models\TaskChecklist;
use Illuminate\Http\Request;

class TaskChecklistController extends Controller
{

    public function store(TaskChecklistStoreRequest $request)
    {
        try {
            $taskChecklist = new TaskChecklist();
            $taskChecklist->title = $request->title;
            $taskChecklist->task_id = $request->taskID;
            $taskChecklist->user_id = \Auth::user()->id;
            $taskChecklist->save();
            \Toastr::success('Checklist added successfully');
            createTaskActivityLog($request->taskID, 'created a new checklist');
        }catch (\Exception $e){
            \Toastr::error('Something went wrong!'. $e->getMessage());
        }
        return back();
    }

    public function changeStatus(Request $request)
    {
        try {
            $taskChecklist = TaskChecklist::findOrFail($request->id);
            $taskChecklist->active_status = !$taskChecklist->active_status;
            $taskChecklist->save();
            createTaskActivityLog($taskChecklist->task_id, 'update the checklist status');
            return response()->json(['message'=> 'Checklist status update successfully', 'status'=> 200]);
        }catch (\Exception $e){
            return response()->json(['message'=> 'Something went wrong!', 'status'=> 500]);
        }
    }

    public function delete(Request $request)
    {
        try {
            $checklist = TaskChecklist::findOrFail($request->id);
            $checklist->delete();
            createTaskActivityLog($checklist->task_id, 'deleted a checklist');
            return response()->json(['message'=> 'Checklist deleted successfully', 'status'=> 200]);
        }catch (\Exception $e){
            return response()->json(['message'=> 'Something went wrong!', 'status'=> 500]);
        }
    }
}
