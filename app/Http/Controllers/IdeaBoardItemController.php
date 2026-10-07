<?php

namespace App\Http\Controllers;

use App\Http\Requests\IdeaBoardItemUpdateRequest;
use App\Http\Requests\IdeaBoardUpdateRequest;
use App\Models\IdeaBoardItem;
use Illuminate\Http\Request;

class IdeaBoardItemController extends Controller
{
    public function store(Request $request, $project_id, $idea_board_id)
    {
        $attribute_name = explode(',', $request->attribute);
        $attribute_value = explode(',', $request->attribute_values);
        $variant_value = [];
        foreach ($attribute_name as $index => $name) {
            $value = [
                'attribute' => $name,
                'value' => $attribute_value[$index],
            ];
            array_push($variant_value, $value);
        }


        $ideaBoardItem = new IdeaBoardItem();
        $ideaBoardItem->idea_board_id = $idea_board_id;
        $ideaBoardItem->type = (int)$request->service_type;
        if ((int)$request->service_type === 1) {
            $ideaBoardItem->product_id = $request->product_id;
            $ideaBoardItem->variation = $variant_value;
        } else {
            $ideaBoardItem->project_service_id = $request->product_id;
        }
        $ideaBoardItem->unit_price = $request->price;
        $ideaBoardItem->discount_type = 1;
        $ideaBoardItem->discount_amount = 0;
        $ideaBoardItem->price = $request->price;
        $ideaBoardItem->quantity = 1;

        $ideaBoardItem->save();


        return response()->json(['message' => 'Item Added successfully.', 'data' => $ideaBoardItem], 200);
    }

    public function edit($project_id, $idea_board_id)
    {
        $idea_board_item = IdeaBoardItem::with('product', 'service')->where('id', $idea_board_id)->first();
        $data = [
            'id' => $idea_board_item->id,
            'name' => $idea_board_item->product ? $idea_board_item->product->name : $idea_board_item->service->title,
            'image' => $idea_board_item->product ? getFilePath($idea_board_item->product->thumbnail_img) : getFilePath($idea_board_item->service->image),
            'unit_price' => $idea_board_item->unit_price,
            'quantity' => $idea_board_item->quantity,
            'price' => $idea_board_item->price,
            'variation' => $idea_board_item->variation ?? [],
            'type' => $idea_board_item->type,
            'markup' => $idea_board_item->markup,
            'shipping_charge' => $idea_board_item->shipping_charge,
            'discount_amount' => $idea_board_item->discount_amount,
            'discount_type' => $idea_board_item->discount_type,
        ];
        return response()->json(['data' => $data, 'status' => 200], 200);
    }

    public function update(IdeaBoardItemUpdateRequest $request, $project_id, $idea_board_id)
    {
        $idea_board_item = IdeaBoardItem::find($request->item_id);
        $idea_board_item->unit_price = $request->unit_price;
        $idea_board_item->discount_type = $request->discount_type;
        $idea_board_item->shipping_charge = $request->shipping_charge;
        $idea_board_item->discount_amount = $request->discount;
        $idea_board_item->markup = 0;
        $idea_board_item->price = $request->total_price;
        $idea_board_item->quantity = $request->quantity;

        $idea_board_item->save();
        \Toastr::success('Item updated successfully.', 'Success');

        return response()->json(['message' => 'Item Updated', 'status' => 200], 200);
    }

    public function destroy(Request $request, $project_id, $idea_board_id)
    {
        $idea_board_item = IdeaBoardItem::find($request->item_id);
        $idea_board_item->delete();
        return response()->json(['text' => 'Item Deleted Successfully', 'icon' => 'success'], 200);
    }
}
