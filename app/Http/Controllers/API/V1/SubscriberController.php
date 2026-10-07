<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriberMailStoreRequest;
use App\Models\Subscriber;
use Exception;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{

    public function store(SubscriberMailStoreRequest $request)
    {
        try {
            $subscriber = new Subscriber();
            $subscriber->email = $request->email;
            $subscriber->save();
            return sendResponse('Subscribed successfully');
        }catch (Exception $e){
            return sendError('Something went wrong!', $e);
        }
    }
}
