<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Task;
use App\Models\Project;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Traits\FileUploadTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DocumentStoreRequest;
use App\Http\Requests\DocumentUpdateRequest;

class DocumentController extends Controller
{

    use FileUploadTrait;

    public function index(Request $request, $source, $source_id = null)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $document = Document::where('source', $source)
            ->when($source_id, function ($query) use ($source_id) {
                return $query->where('source_id', $source_id);
            }, function ($query) {
                return $query->whereNull('source_id')->where('created_by', getUserId());
            })
            ->when($search, function ($query) use ($search) {
                return $query->where('title', 'LIKE', "%{$search}%");
            });

        if ($status != '') {
            $document->where('active_status', (int)$status);
        }

        $document = $document->latest()->get();

        if ($source_id) {
            if ($source === 'project') {
                $data = Project::select('title', 'id')->where('id', $source_id)->first();
            } else {
                $data = Task::select('title', 'id')->where('id', $source_id)->first();
            }
        } else {
            $data = null;
        }

        return view('project-management.project.projects.document', compact('document', 'source', 'source_id', 'data', 'search', 'status'));
    }


    public function store(DocumentStoreRequest $request)
    {
        try {
            $document = new Document();
            $document->title = $request->title;
            $document->source = $request->source;
            $document->source_id = $request->source_id;
            $document->active_status = $request->status;
            $document->created_by = Auth::user()->id;
            $path = "";
            if ($request->source_id != null) {
                $path = $request->source . '/' . $request->source_id;
            } else {
                $path = $request->source;
            }

            $document->file = $this->uploadFile($request->file('file'), $path);
            $document->save();
            Toastr::success('Document uploaded successfully');
        } catch (\Exception $e) {
            Toastr::error('Something went wrong!' . $e->getMessage());
        }

        return back();
    }

    public function edit(Request $request)
    {
        $document = Document::where('source', $request->source)
            ->where('id', $request->id)
            ->first();

        $document->file = getFileElement(getFilePath($document->file));
        return response()->json($document);
    }

    public function update(DocumentUpdateRequest $request)
    {
        try {
            $document = Document::where('source', $request->source)
                ->where('id', $request->id)
                ->first();

            $document->title = $request->title;
            $document->source = $request->source;
            $document->source_id = $request->source_id;
            $document->active_status = $request->status;
            $document->updated_by = Auth::user()->id;

            if ($request->hasFile('file')) {
                $this->deleteFile($document->file);
                $path = "";
                if ($request->source_id != null) {
                    $path = $request->source . '/' . $request->source_id;
                } else {
                    $path = $request->source;
                }
                $document->file = $this->uploadFile($request->file('file'), $path);

            }
            $document->save();
            Toastr::success('Document updated successfully');
        } catch (Exception $e) {
            Toastr::error('Something went wrong!');
        }
        return back();

    }

    public function delete(Request $request)
    {
        try {
            $document = Document::findOrFail($request->id);
            $this->deleteFile($document->file);
            $document->delete();
            return response()->json(['message' => 'Document deleted successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong!', 'status' => 500]);
        }
    }

    public function changeStatus(Request $request)
    {
        $data = Document::findOrFail($request->id);
        $data->active_status = !$data->active_status;
        $data->save();
        return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
    }


}
