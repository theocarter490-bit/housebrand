<?php

namespace App\Http\Controllers;

use App\Http\Requests\IdeaBoardCreateRequest;
use App\Http\Requests\IdeaBoardUpdateRequest;
use App\Http\Requests\IdValidationRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\Category;
use App\Models\IdeaBoard;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class IdeaBoardController extends Controller
{
    use FileUploadTrait;

    //
    public function index($project_id)
    {
        $project = Project::find($project_id);
        $ideaBoard = IdeaBoard::where('project_id', $project_id)->withSum('items', 'price')->paginate(perPage());

        return view('project-management.project.projects.idea-board.index', compact('project', 'ideaBoard'));
    }

    public function details($project_id, $id)
    {
        $ideaBoard = IdeaBoard::with(['project', 'items' => function ($query) {
            $query->with(['product', 'service']);
        }])->withSum('items', 'price')->find($id);


        $ideaBoardItem = [];

        foreach ($ideaBoard->items as $item) {
            $temp = [
                'id' => $item->id,
                'name' => $item->product ? $item->product->name : $item->service->title,
                'image' => $item->product ? $item->product->thumbnail_img : $item->service->image,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'total_price' => $item->price,
                'discount_price' => $item->discount_amount_value,
                'shipping_charge' => $item->shipping_charge,
                'mark_up' => $item->markup,
                'variation' => $item->variation ?? [],
                'type' => $item->type,

            ];
            array_push($ideaBoardItem, $temp);
        }
        $services = ProjectService::where('active_status', 1)->where('user_id', getUserId())->get();

        return view('project-management.project.projects.idea-board.details', compact('ideaBoard', 'services', 'ideaBoardItem'));
    }

    public function store(IdeaBoardCreateRequest $request, $project_id)
    {
        try {

            $category = new IdeaBoard();

            $category->title = $request->title;
            $category->description = $request->description;
            $category->budget = $request->budget;
            $category->active_status = $request->status == '1' ? 1 : 0;
            $category->code = 'IDB-' . time();
            $category->user_id = Auth::user()->id;
            $category->project_id = $project_id;

            if ($request->hasFile('image')) {
                $path = $this->uploadFile($request->file('image'), 'project/' . $project_id . '/idea-board');
                $category->image = $path;
            }

            $category->save();

            return response()->json(['message' => 'Idea Board Created Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            dd($e);
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function edit($project_id, $id)
    {
        $ideaBoard = IdeaBoard::find($id);
        $ideaBoard = [
            'id' => $ideaBoard->id,
            'name' => $ideaBoard->title,
            'description' => $ideaBoard->description,
            'active_status' => $ideaBoard->active_status,
            'budget' => $ideaBoard->budget,
            'image' => getFilePath($ideaBoard->image),
        ];

        return response()->json(['data' => $ideaBoard, 'status' => 200], 200);
    }

    public function update(IdeaBoardUpdateRequest $request, $project)
    {

        try {

            $idea_board = IdeaBoard::find($request->idea_board_id);

            $idea_board->title = $request->title;
            $idea_board->description = $request->description;
            $idea_board->budget = $request->budget;
            $idea_board->active_status = $request->status == '1' ? 1 : 0;

            if ($request->hasFile('image')) {
                $path = $this->uploadFile($request->file('image'), 'project/' . $idea_board->project_id . '/idea-board');

                if ($idea_board->image) {
                    $this->deleteFile($idea_board->image);
                }
                $idea_board->image = $path;
            }

            $idea_board->save();

            return response()->json(['message' => 'Idea Board Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $idea_board = IdeaBoard::find($request->idea_board_id);

            if ($idea_board->image) {
                $this->deleteFile($idea_board->image);
            }

            $idea_board->delete();
            return response()->json(['text' => 'Idea Board has been deleted Successfully.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function getService($id)
    {
        $projectService = ProjectService::find($id);
        $projectService->image = getFilePath($projectService->image);
        return $projectService;
    }

    public function getProducts()
    {
        $products = Product::where('is_published', 1)->isClient()->get();
        return response()->json(['products' => $products]);
    }

    public function getManufacturerProducts()
    {
        $manufactureProducts = Product::where('is_published', 1)
            ->where(function ($q) {
                $q->whereIn('user_id', getSellerIds());
            })->get();

        return response()->json(['products' => $manufactureProducts]);
    }

}
