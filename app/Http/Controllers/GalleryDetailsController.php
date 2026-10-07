<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalleryDetailsStoreRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\Gallery;
use App\Models\GalleryDetails;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;


class GalleryDetailsController extends Controller
{
    use FileUploadTrait;

    public function index($gallery_id)
    {
        $gallery = Gallery::with('details')->find($gallery_id);
        return view('gallery.details', compact('gallery'));
    }

    public function store(GalleryDetailsStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $existing_ids = [];


            $galleryData = $request->data ?? [];


            foreach ($galleryData as $key => $gallery) {
                if (!empty($gallery['id'])) {
                    $existing_ids[] = $gallery['id'];
                }
            }


            $details_ids = GalleryDetails::where('gallery_id', $request->gallery_id)
                ->pluck('id')
                ->toArray();


            $result = array_diff($details_ids, $existing_ids);


            $itemsToDelete = GalleryDetails::findMany($result);

            foreach ($itemsToDelete as $item) {
                if ($item->image) {
                    $this->deleteFile($item->image);
                }
                $item->delete();
            }


            if (empty($galleryData)) {
                DB::commit();
                Toastr::success('All gallery details deleted successfully');
                return redirect()->route('gallery.index');
            }

            foreach ($galleryData as $key => $gallery) {
                $details = isset($gallery['id']) && $gallery['id']
                    ? GalleryDetails::find($gallery['id'])
                    : new GalleryDetails();

                $details->title = $gallery['title'] ?? null;
                $details->details = $gallery['description'] ?? null;
                $details->gallery_id = $request->gallery_id;

                if (!empty($gallery['image'])) {
                    $path = $this->uploadFile($gallery['image'], 'gallery');
                    if ($details->image) {
                        $this->deleteFile($details->image);
                    }
                    $details->image = $path;
                }

                $details->save();
            }

            DB::commit();

            Toastr::success('Gallery details updated successfully');
            return redirect()->route('gallery.index');
        } catch (Exception $e) {
            DB::rollBack();
            Toastr::error('Something went wrong');
            return back();
        }
    }


    public function storeSingleDetails(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'gallery_id' => 'required|exists:galleries,id',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif',
        ], [
            'title.required' => 'Title is required',
            'title.max' => 'Title cannot exceed 255 characters',
            'gallery_id.required' => 'Please select a gallery',
            'gallery_id.exists' => 'Selected gallery does not exist',
            'image.required' => 'Image is required',
            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be jpeg, jpg, png, or gif format',
            'image.max' => 'Image size cannot exceed 800KB',
            'description.max' => 'Description cannot exceed 1000 characters'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 403
            ], 200);
        }

        DB::beginTransaction();
        try {
            $details = new GalleryDetails();

            $details->title = $request->title;
            $details->details = $request->description;
            $details->gallery_id = $request->gallery_id;

            if ($request->hasFile('image')) {
                $path = $this->uploadFile($request->file('image'), 'gallery');
                $details->image = $path;
            }

            $details->save();
            DB::commit();

            return response()->json(['message' => 'Image Created Successfully.', 'status' => 200], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong',
                'status' => 500
            ], 500);
        }
    }

}
