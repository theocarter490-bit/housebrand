<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePageRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\FooterWidget;
use App\Models\Page;
use App\Models\PageSectionItems;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Page::with(['footer', 'user', 'createdBy', 'updatedBy'])->byShop();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('pages_status_change')) {
                        $isChecked = $row->active_status == 1 ? 'checked' : '';
                        $statusHtml .= '
                        <label class="switch switch-success" style="margin-bottom: 5px;">
                            <input type="checkbox" class="switch-input changeStatus" data-id="' . $row->id . '" ' . $isChecked . ' />
                            <span class="switch-toggle-slider">
                                <span class="switch-on">
                                    <i class="ti ti-check"></i>
                                </span>
                                <span class="switch-off">
                                    <i class="ti ti-x"></i>
                                </span>
                            </span>
                        </label>
                    ';
                    }

                    // Status badge, displayed below the toggle switch if it’s shown
                    $statusHtml .= '<div><span class="badge ' . $statusBadgeClass . '">' . $statusLabel . '</span></div>';

                    $statusHtml .= '</div>'; // Closing the main container

                    return $statusHtml;
                })
                ->addColumn('info', function ($row) {
                    return dataInfo($row);
                })
                ->addColumn('user_name', function ($row) {
                    return $row->user->name;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('active_status', $request->get('status'));
                    }

                    if ($request->get('sending_status') == '0' || $request->get('sending_status') == '1') {
                        $instance->where('is_sent', $request->get('sending_status'));
                    }
                }, true)
                ->addColumn('footer_name', function ($row) {
                    return $row->footer->title;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (hasPermission('pages_update')) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">' .
                            '<a href="' . route('cms.pages.edit', $row->id) . '" class="dropdown-item" ><i class="ti ti-edit" ></i> Edit</a>' .
                            '</div>' .
                            '</div>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'image', 'status', 'content', 'info'])
                ->make(true);
        }
        return view('frontend-cms.pages.index');
    }

    public function edit($id)
    {
        $data = Page::with('items')->find($id);
        $footer_widget = FooterWidget::all();
        return view('frontend-cms.pages.edit', compact('data', 'footer_widget'));
    }

    public function update(UpdatePageRequest $request)
    {
        try {
            DB::beginTransaction();
            $page = Page::find($request->id);
            $footer_widget = FooterWidget::find($request->footer_widget);
            $page->title = $request->title;
            // $page->slug = Str::slug($request->title);
            $page->footer_widget_id = $footer_widget->id;
            $page->short_desc = $request->short_desc;
            $page->content = $request->content_data;
            $page->our_mission = $request->our_mission;
            $page->meta_title = $request->title;
            $page->active_status = $request->status;
            $page->meta_description = $request->content_data;
            $page->update();

            if ($request->has('sectionItems')) {
                $updated_ids = [];
                $exiting_item_ids = PageSectionItems::where('page_id', $page->id)->pluck('id')->toArray();

                foreach ($request->sectionItems as $key => $sectionItem) {
                    $section = new PageSectionItems();
                    if ($sectionItem['id'] && $sectionItem['id'] != null && $sectionItem['id'] != 'null' && $sectionItem['id'] != '') {
                        $section = PageSectionItems::find($sectionItem['id']);
                        array_push($updated_ids, $section->id);
                    }
                    $section->page_id = $page->id;
                    $section->name = $sectionItem['title'];
                    $section->description = $sectionItem['description'];

                    if ($request['sectionItems'][$key]['image'] != 'undefined' && $request['sectionItems'][$key]['image']) {
                        $this->deleteFile($section->image);
                        $path = $this->uploadFile($request['sectionItems'][$key]['image'], 'pageItems');
                        $section->image = $path;
                    }
                    $section->save();
                }

                $result = array_diff($exiting_item_ids, $updated_ids);
                $itemsToDelete = PageSectionItems::findMany($result);

                foreach ($itemsToDelete as $item) {
                    if ($item->image) {
                        $this->deleteFile($item->image);
                    }
                    $item->delete();
                }
            }

            DB::commit();

            return response()->json(['message' => 'Page Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }


    public function changeStatus(Request $request)
    {
        $data = Page::find($request->id);
        if ($data) {
            if ($data->active_status == 1) {
                $data->active_status = 0;
            } else {
                $data->active_status = 1;
            }
            $data->save();
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200], 200);
        } else {
            return response()->json(['message' => "Data Not Found!"], 404);
        }
    }
}
