<?php

namespace App\Http\Controllers;

use App\Http\Requests\IdValidationRequest;
use App\Http\Requests\SectionStoreRequest;
use App\Http\Requests\SectionUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\FileUploadTrait;
use App\Models\SpecialSection;
use App\Models\SpecialSectionCategory;
use Illuminate\Support\Str;


class SpecialSectionController extends Controller
{
    use FileUploadTrait;

    public function index($section_type, Request $request)
    {
        if ($request->ajax()) {
            $data = SpecialSection::with(['category', 'createdBy.shop', 'updatedBy.shop'])->where('type', $section_type)->byShop()->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('image', function ($row) {
                    return "<img src='" . getFilePath($row->image) . "' alt='' width='50px' height='50px' />";
                })
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->is_active == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->is_active == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('portfolio_inspiration_status_change')) {
                        $isChecked = $row->is_active == 1 ? 'checked' : '';
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
                ->addColumn('category', function ($row) {
                    return $row->category ? $row->category->name : '';
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('is_active', $request->get('status'));
                    }
                    if ($request->get('filter_category') != '') {
                        $instance->where('special_section_category_id', $request->get('filter_category'));
                    }
                }, true)
                ->addColumn('info', function ($row) {
                    return dataInfo($row);
                })
                ->addColumn('action', function ($row) use ($section_type) {
                    $btn = '<div class="d-inline-block text-nowrap">' .
                        '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                        '<div class="dropdown-menu dropdown-menu-end m-0">';

                    if ($section_type == 1 && hasPermission('read_portfolio_inspiration_description')) {
                        $btn .= '<a href="' . route('section.portfolioAndInspiration.details.create', $row->id) . '" class="dropdown-item btn btn-primary" ><i class="ti ti-file"></i> Description</a>';
                    } elseif ($section_type == 2 && hasPermission('read_portfolio_inspiration_description')) {
                        $btn .= '<a href="' . route('section.portfolioAndInspiration.details.create', $row->id) . '" class="dropdown-item btn btn-primary" ><i class="ti ti-file"></i> Description</a>';
                    }

                    if ($section_type == 1 && hasPermission('portfolio_and_inspiration_update')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item brand_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBrandEditModal" data-id="' . $row->id . '"><i class="ti ti-edit"></i> Edit</a>';
                    } elseif ($section_type == 2 && hasPermission('portfolio_and_inspiration_update')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item brand_edit_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBrandEditModal" data-id="' . $row->id . '"><i class="ti ti-edit"></i> Edit</a>';
                    }

                    if ($section_type == 1 && hasPermission('portfolio_inspiration_delete')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item brand_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    } else if ($section_type == 2 && hasPermission('portfolio_inspiration_delete')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item brand_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'image', 'status', 'info'])
                ->make(true);
        }

        $special_categories = SpecialSectionCategory::where('is_active', 1)->byShop()->get();

        $view = 'section.portfolio.index';
        if ($section_type == 2) {
            $view = 'section.inspiration.index';
        }
        return view($view, compact('special_categories'));
    }

    public function store(SectionStoreRequest $request, $section_type)
    {
        if (moduleConditionLimitCheck('portfolio-inspiration', 'App\Models\SpecialSection') == false) {
            return response()->json(['message' => 'You have reached the maximum quantity for this module.', 'status' => 403], 500);
        }
        try {

            $specialSection = new SpecialSection();

            $specialSection->title = $request->name;
            $specialSection->is_active = $request->status == '1' ? 1 : 0;
            $specialSection->slug = Str::slug($request->name);
            $specialSection->special_section_category_id = $request->category_id;
            $specialSection->type = $section_type;
            $specialSection->user_id = getUserId();
            $specialSection->created_by = Auth::user()->id;

            if ($request->hasFile('image')) {
                $path = $this->uploadFile($request->file('image'), 'portfolioAndInspiration');
                $specialSection->image = $path;
            }
            if ($request->hasFile('image2')) {
                $path = $this->uploadFile($request->file('image2'), 'portfolioAndInspiration');
                $specialSection->image2 = $path;
            }

            $specialSection->save();

            if ($section_type == 1) {
                $sectionName = "Portfolio";
            } else {
                $sectionName = "Inspiration";
            }

            removeDataFromRedisAPI(['special_section_list', getUserId()]);

            return response()->json(['message' => "$sectionName Created Successfully", 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function edit($section_type, $id)
    {
        $data = SpecialSection::find($id);
        $data = [
            'id' => $data->id,
            'title' => $data->title,
            'special_section_category_id' => $data->special_section_category_id,
            'is_active' => $data->is_active,
            'image' => getFilePath($data->image),
            'image2' => getFilePath($data->image2),
        ];
        return response()->json(['data' => $data, 'status' => 200], 200);
    }


    public function update(SectionUpdateRequest $request, $section_type)
    {

        try {

            $spectialSection = SpecialSection::find($request->section_id);

            $spectialSection->title = $request->name;
            $spectialSection->is_active = $request->status == '1' ? 1 : 0;
            $spectialSection->slug = Str::slug($request->name);
            $spectialSection->special_section_category_id = $request->category_id;

            $spectialSection->updated_by = Auth::user()->id;

            if ($request->hasFile('image')) {
                $path = $this->uploadFile($request->file('image'), 'portfolioAndInspiration');

                if ($spectialSection->image) {
                    $this->deleteFile($spectialSection->image);
                }
                $spectialSection->image = $path;
            }
            if ($request->hasFile('image2')) {
                $path = $this->uploadFile($request->file('image2'), 'portfolioAndInspiration');

                if ($spectialSection->image2) {
                    $this->deleteFile($spectialSection->image2);
                }
                $spectialSection->image2 = $path;
            }

            $spectialSection->save();

            if ($section_type == 1) {
                $sectionName = "Portfolio";
            } else {
                $sectionName = "Inspiration";
            }
            removeDataFromRedisAPI(['special_section_list', getUserId()]);

            return response()->json(['message' => "$sectionName Updated Successfully", 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function destroy(IdValidationRequest $request, $section_type)
    {
        try {
            $spectialSection = SpecialSection::find($request->section_id);

            if ($spectialSection->image != null && $spectialSection->image) {
                $this->deleteFile($spectialSection->image);
            }
            if ($spectialSection->image2 != null && $spectialSection->image2) {
                $this->deleteFile($spectialSection->image2);
            }

            $spectialSection->delete();
            if ($section_type == 1) {
                $sectionName = "Portfolio";
            } else {
                $sectionName = "Inspiration";
            }
            removeDataFromRedisAPI(['special_section_list', getUserId()]);
            return response()->json(['text' => "$sectionName has been deleted.", 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function changeStatus(Request $request)
    {
        $data = SpecialSection::find($request->id);
        if ($data) {
            $data->is_active = !$data->is_active;
            $data->save();
            removeDataFromRedisAPI(['special_section_list', getUserId()]);
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } else {
            return response()->json(['message' => 'Data Not Found!', 'status' => 404]);
        }
    }
}
