<?php

namespace App\Http\Controllers\API\V1;

use App\Models\IdeaBoard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\IdeaBoardResource;
use Illuminate\Support\Facades\Validator;

class IdeaBoardController extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }
        $ideaBoards = IdeaBoard::whereHas('project', function ($query) {
            $query->where('client_id', Auth::user()->id);
        })->where('project_id', $request->project_id);

        if ($request->filled('search')) {
            $ideaBoards->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $ideaBoards = $ideaBoards->withSum('items', 'price')->paginate(perPage());

        return sendResponse('Idea Board List', IdeaBoardResource::collection($ideaBoards)->resource);
    }

    public function details($id)
    {
        $ideaBoard = IdeaBoard::where('id', $id)->withSum('items', 'price')->with(['items' => function ($query) {
            $query->with('service', 'product');
        }, 'project'])->first();

        return sendResponse('Idea board details', new IdeaBoardResource($ideaBoard));
    }
}
