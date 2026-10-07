<?php

namespace App\Http\Controllers;


use App\Events\MessageSent;
use App\Models\Role;
use App\Models\User;
use App\Models\ChatMessage;
use Google\Service\Batch\Message;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;

use App\Events\PrivateMessageSent;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ChatUsersResource;
use App\Http\Resources\ChatMessageResource;
use Google\Auth\Credentials\UserRefreshCredentials;

class ChatController extends Controller
{
    use FileUploadTrait;

    public function index()
    {

    }

    public function store(Request $request)
    {
        try {
            $fromUser = auth()->user();
            $toUserId = $request->input('to_id');
            $messageText = $request->input('message');
            $path = null;

            if ($request->hasFile('attach_file')) {
                $path = $this->uploadFile($request->file('attach_file'), 'chat');
            }

            // Save message
            ChatMessage::create([
                'from_id' => $fromUser->id,
                'to_id' => $toUserId,
                'message' => $messageText,
                'file' => $path,
            ]);

            $toUSer = User::find($toUserId);

            // Broadcast event
            broadcast(new PrivateMessageSent(
                $messageText,
                new ChatUsersResource(Auth::user()),
                new ChatUsersResource($toUSer),
                $path ? getFilePath($path) : null,
            ));

            return response(['status' => 'sent']);
        } catch (\Exception $e) {
            return response(['status' => 'error']);
        }

    }

    public function recentUsers(Request $request, $user_id)
    {
        $directChatUserId = $request->query('directChatUser');
        $directChatUser = null;

        if ($directChatUserId) {
            $directChatUser = User::select('users.id', 'users.name', 'users.avatar', 'users.email', 'roles.name as role_name')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('users.id', $directChatUserId)
                ->where('users.id', '!=', $user_id)
                ->first();
        }

        $recentChatUsers = User::select('users.id', 'users.name', 'users.avatar', 'users.email', 'roles.name as role_name')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->join('chat_messages', function ($join) use ($user_id) {
                $join->on('users.id', '=', 'chat_messages.from_id')->where('chat_messages.to_id', $user_id)
                    ->orOn('users.id', '=', 'chat_messages.to_id')->where('chat_messages.from_id', $user_id);
            });

        if ($directChatUserId) {
            $recentChatUsers = $recentChatUsers->where('users.id', '!=', $directChatUserId);
        }

        if ($request->query('search')) {
            $recentChatUsers = $recentChatUsers->where('users.name', 'like', '%' . $request->query('search') . '%');
        }

        $recentChatUsers = $recentChatUsers->where('users.id', '!=', $user_id)
            ->groupBy('users.id', 'users.name', 'users.avatar', 'users.email', 'roles.name')
            ->orderByDesc(DB::raw('MAX(chat_messages.created_at)'))
            ->paginate(20);

        $recentChatUsersArray = $recentChatUsers->items();

        if ($directChatUser) {
            array_unshift($recentChatUsersArray, $directChatUser);
        }

        $recentChatUsers->setCollection(collect($recentChatUsersArray));

        return sendResponse('users', ChatUsersResource::collection($recentChatUsers)->resource);
    }

    public function allUsers(Request $request, $id)
    {
        $user_id = Auth::user()->id;
        $allUsers = User::query();

        if ($request->query('search')) {
            $allUsers = $allUsers->where('name', 'like', '%' . $request->query('search') . '%');
        }

        if (Auth::user()->role_id == Role::MANUFACTURER) {
            $allUsers = $allUsers->whereIn('role_id', [Role::DESIGNER, Role::SUPER_ADMIN]);
        } elseif (Auth::user()->role_id == Role::DESIGNER) {
            $allUsers = $allUsers->where(function ($query) {
                $query->whereIn('role_id', [Role::MANUFACTURER, Role::SUPER_ADMIN, Role::DESIGNER])
                    ->orWhere('supervisor_id', getUserId())
                    ->orWhere('designer_id', getUserId());
            })->where('id', '!=', $user_id);
        } elseif (Auth::user()->role_id == Role::CUSTOMER) {
            $allUsers = $allUsers->where('id', Auth::user()->designer_id);
        } elseif (Auth::user()->role_id == Role::ADMIN || Auth::user()->role_id == Role::SUPER_ADMIN) {
            $allUsers = $allUsers->whereIn('role_id', [Role::MANUFACTURER, Role::DESIGNER, Role::CUSTOMER])
                ->orWhere('designer_id', getUserId())
                ->orWhere('supervisor_id', getUserId())
                ->where('id', '!=', $user_id);
        } else {
            $allUsers = $allUsers->where('id', getUserId())
                ->orWhere('supervisor_id', getUserId())
                ->where('id', '!=', $user_id);
        }

        $allUsers = $allUsers->paginate(20);

        return sendResponse('users', ChatUsersResource::collection($allUsers)->resource);
    }

    public function messages($user_id)
    {
        $currentUserId = auth()->id();

        $messages = ChatMessage::where(function ($query) use ($currentUserId, $user_id) {
            $query->where('from_id', $currentUserId)->where('to_id', $user_id);
        })->orWhere(function ($query) use ($currentUserId, $user_id) {
            $query->where('from_id', $user_id)->where('to_id', $currentUserId);
        })->latest()->paginate(30);

        return sendResponse('messages', ChatMessageResource::collection($messages)->resource);
    }

}
