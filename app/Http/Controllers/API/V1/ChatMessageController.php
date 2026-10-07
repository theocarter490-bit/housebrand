<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Role;
use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use App\Events\PrivateMessageSent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ChatUsersResource;
use App\Http\Resources\ChatMessageResource;

class ChatMessageController extends Controller
{
    use FileUploadTrait;

    public function recentUsers()
    {
        $user_id = Auth::user()->id;
        $recentChatUsers = User::select('users.id', 'users.name', 'users.avatar', 'users.email', 'roles.name as role_name')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->join('chat_messages', function ($join) use ($user_id) {
                $join->on('users.id', '=', 'chat_messages.from_id')->where('chat_messages.to_id', $user_id)
                    ->orOn('users.id', '=', 'chat_messages.to_id')->where('chat_messages.from_id', $user_id);
            })
            ->where('users.id', '!=', $user_id)
            ->groupBy('users.id', 'users.name', 'users.avatar', 'users.email')
            ->orderByDesc(DB::raw('MAX(chat_messages.created_at)'))
            ->get();


        return sendResponse('users', ['recent_chat' => ChatUsersResource::collection($recentChatUsers)]);
    }

    public function allUsers()
    {
        $user_id = Auth::user()->id;
        $allUsers = User::with('role');
        $search = null;

        if (Auth::user()->role_id == Role::MANUFACTURER) {
            $allUsers = $allUsers->whereIn('role_id', [Role::DESIGNER, Role::SUPER_ADMIN]);
        }elseif (Auth::user()->role_id == Role::DESIGNER) {
            $allUsers = $allUsers->where(function ($query) {
                $query->whereIn('role_id', [Role::MANUFACTURER, Role::SUPER_ADMIN, Role::DESIGNER])
                    ->orWhere('supervisor_id', getUserId())
                    ->orWhere('designer_id', getUserId());
            })->where('id', '!=', $user_id);
        }elseif (Auth::user()->role_id == Role::CUSTOMER) {
            $allUsers = $allUsers->where('id', Auth::user()->designer_id);
        } elseif (Auth::user()->role_id == Role::ADMIN || Auth::user()->role_id == Role::SUPER_ADMIN) {
            $allUsers = $allUsers->whereIn('role_id', [Role::MANUFACTURER, Role::DESIGNER])
                ->orWhere('designer_id', getUserId())
                ->orWhere('supervisor_id', getUserId())
                ->where('id', '!=', $user_id);
        }

        if (request()->has('search')) {
            $search = request()->input('search');
            $allUsers = $allUsers->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allUsers = $allUsers->paginate(20);
        return sendResponse('users', ['allUsers' => ChatUsersResource::collection($allUsers)->resource , 'search' => $search]);
    }

    public function sendMessage(Request $request)
    {
        try {
            $fromUser = Auth::user()->id;
            $toUserId = $request->input('to_id');
            $messageText = $request->input('message');
            $path = null;

            if ($request->hasFile('attach_file')) {
                $path = $this->uploadFile($request->file('attach_file'), 'chat');
            }

            // Save message
            ChatMessage::create([
                'from_id' => $fromUser,
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

    public function messages(Request $request)
    {
        $currentUserId = Auth::user()->id;
        $user_id = $request->input('user_id');

        $messages = ChatMessage::where(function ($query) use ($currentUserId, $user_id) {
            $query->where('from_id', $currentUserId)->where('to_id', $user_id);
        })->orWhere(function ($query) use ($currentUserId, $user_id) {
            $query->where('from_id', $user_id)->where('to_id', $currentUserId);
        })->latest()->paginate(30);

        return sendResponse('messages', ChatMessageResource::collection($messages)->resource);
    }
}
