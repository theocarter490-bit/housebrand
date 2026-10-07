<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LiveChatController extends Controller
{
    public function chats()
    {
        return view('live-chat.index');
    }
}
