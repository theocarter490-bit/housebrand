<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Http\Traits\FileUploadTrait;
use App\Models\Document;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {

        $document = Document::where('source', 'project')->where('active_status', 1)->where('source_id', $request->project_id);
        if ($request->filled('search')) {
            $document->where('title', 'like', '%' . $request->search . '%');
        }
        $document = $document->paginate(perPage());
        return sendResponse('Project Document List', DocumentResource::collection($document)->resource);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:51200',
            'project_id' => 'required'
        ]);
        if ($validator->fails()) {
            return sendError('Validation Error', $validator->errors(), 403);
        }
        try {
            $document = new Document();
            $document->title = $request->title;
            $document->source = 'project';
            $document->source_id = $request->project_id;
            $document->active_status = 1;
            $document->created_by = Auth::user()->id;
            $path = "";
            if ($request->project_id != null) {
                $path = 'project' . '/' . $request->project_id;
            } else {
                $path = 'project';
            }

            $document->file = $this->uploadFile($request->file('file'), $path);
            $document->save();
            return sendResponse('Document uploaded successfully');
        } catch (\Exception $e) {
            return sendError('Something went wrong');
        }
    }

    public function destroy($id)
    {
        try {
            $document = Document::where('created_by',Auth::user()->id)->where('id', $id)->first();
            if (!$document) {
                return sendError('Document not found', [], 404);
            }
            $document->delete();
            return sendResponse('Document deleted successfully');
        } catch (\Exception $e) {
            return sendError('Something went wrong');
        }
    }
}
