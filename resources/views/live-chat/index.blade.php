@extends('layouts.master')

@section('title', $title ?? _trans('keyword.Live Chat'))
@push('styles')
    <style>
        .unseen-message {
            background-color: #ededed !important;
        }

        /* Better approach using CSS class */
        .chat-contact-list {
            max-height: calc(calc(100vh - 11.5rem) - 3.5rem);
            overflow-y: auto;
        }

        /* Optional: Custom scrollbar styling */
        .chat-contact-list::-webkit-scrollbar {
            width: 6px;
        }

        .chat-contact-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .chat-contact-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .chat-contact-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .sidebar-body {
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Tabs wrapper */
        .nav-tabs-shadow,
        .tab-content,
        .tab-pane {
            height: 100%;
            max-height: 100%;
            overflow: hidden;
        }

        /* Each list should scroll inside the fixed container */
        .chat-contact-list {
            height: 100%;
            max-height: 100%;
            overflow-y: auto;
        }
    </style>
@endpush
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="app-chat card overflow-hidden">
        <!-- Loading State (shown before Vue loads) -->
        <div id="chat-loading" class="vue-loading">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading Chat...</p>
            </div>
        </div>
        <div class="row g-0" id="chat-app">
            <!-- Chat & Contacts -->
            <div class="col app-chat-contacts app-sidebar flex-grow-0 overflow-hidden border-end"
                 id="app-chat-contacts" v-cloak>
                <div class="sidebar-header">
                    <div class="d-flex align-items-center me-3 me-lg-0">
                        <div class="flex-shrink-0 avatar avatar-online me-3" data-bs-toggle="sidebar"
                             data-overlay="app-overlay-ex" data-target="#app-chat-sidebar-left">
                            <img class="user-avatar rounded-circle cursor-pointer"
                                 src="{{getFilePath(auth()->user()->avatar)}}"
                                 alt="Avatar">
                        </div>
                        <div class="flex-grow-1 input-group input-group-merge rounded-pill">
                            <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
                            <input type="search" class="form-control chat-search-input" v-model="searchKeyWord"
                                   v-on:input="handleSearch" placeholder="Search..."
                                   aria-label="Search..." aria-describedby="basic-addon-search31">
                        </div>
                    </div>
                    <i class="ti ti-x cursor-pointer d-lg-none d-block position-absolute mt-2 me-1 top-0 end-0"
                       data-overlay data-bs-toggle="sidebar" data-target="#app-chat-contacts"></i>
                </div>
                <hr class="container-m-nx m-0">
                <div class="sidebar-body" v-cloak>
                    <div class="nav-align-top nav-tabs-shadow">
                        <ul class="nav nav-tabs nav-fill m-0" role="tablist">
                            <li class="nav-item">
                                <button
                                    type="button"
                                    class="nav-link active"
                                    role="tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#navs-justified-home"
                                    aria-controls="navs-justified-home"
                                    aria-selected="true">
                                    <i class="tf-icons ti ti-home ti-xs me-1"></i>Chats
                                    <span v-if="messageFromOtherUserIDs.length>0"
                                          class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-warning ms-1">@{{ messageFromOtherUserIDs.length }}</span>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button
                                    type="button"
                                    class="nav-link"
                                    role="tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#navs-justified-profile"
                                    aria-controls="navs-justified-profile"
                                    aria-selected="false">
                                    <i class="tf-icons ti ti-user ti-xs me-1"></i>Contact: (@{{ totalUsersCount }})
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content p-1" style="max-height: 90%">
                            <!-- Remove the inline style attributes and let the CSS class handle it -->

                            <!-- Recent Chats Tab -->
                            <div class="tab-pane fade show active" id="navs-justified-home" role="tabpanel">
                                <ul class="list-unstyled chat-contact-list" id="chat-list" style="max-height: inherit">
                                    <li class="chat-contact-list-item chat-list-item-0"
                                        :class="{'d-none':recentChatUsers.length>0}">
                                        <h6 class="text-muted mb-0">No Chats Found</h6>
                                    </li>

                                    <li class="chat-contact-list-item border-bottom m-0"
                                        v-for="(user, index) in recentChatUsers"
                                        :key="user.id"
                                        :class="[
              { active: selectedUser?.id === user.id },
              isMessageFromOtherUser(user.id) ? 'unseen-message' : ''
            ]"
                                        @click="selectUser(user)">
                                        <a class="d-flex align-items-center ">
                                            <div class="flex-shrink-0 avatar"
                                                 :class="[ isOnline(user.id)?'avatar-online':'avatar-offline' ]">
                                                <img :src="user.avatar" alt="Avatar" class="rounded-circle">
                                            </div>
                                            <div class="chat-contact-info flex-grow-1 ms-2">
                                                <h6 class="chat-contact-name text-truncate m-0">@{{ user.name }}</h6>
                                                <span class="badge chat-contact-status text-truncate mb-0"
                                                      :class="{
                            'text-info': user.role === 'Designer',
                            'text-primary': user.role === 'Manufacturer',
                            'text-warning': user.role === 'Customer',
                            'text-danger': user.role === 'Super Admin',
                          }">
                        @{{ user.role }}
                    </span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- All Contacts Tab -->
                            <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
                                <ul class="list-unstyled chat-contact-list mb-0"
                                    id="contact-list" ref="contactList" @scroll="handleContactScroll">
                                    <li class="chat-contact-list-item contact-list-item-0 "
                                        :class="{'d-none':allUsers.length>0}">
                                        <h6 class="text-muted mb-0">No Contacts Found</h6>
                                    </li>
                                    <li v-if="allUsersLoading" class="text-center p-2">
                                        <span class="spinner-border spinner-border-sm"></span> Loading...
                                    </li>
                                    <li class="chat-contact-list-item m-0 border-bottom"
                                        v-for="(user, index) in allUsers"
                                        :key="user.id"
                                        :class="{ active: selectedUser?.id === user.id }"
                                        @click="selectUser(user)">
                                        <a class="d-flex align-items-center">
                                            <div class="flex-shrink-0 avatar"
                                                 :class="[ isOnline(user.id)?'avatar-online':'avatar-offline' ]">
                                                <img :src="user.avatar" alt="Avatar" class="rounded-circle">
                                            </div>
                                            <div class="chat-contact-info flex-grow-1 ms-2">
                                                <h6 class="chat-contact-name text-truncate m-0">@{{ user.name }}</h6>
                                                <span class="badge chat-contact-status text-truncate mb-0"
                                                      :class="{
                            'text-info': user.role === 'Designer',
                            'text-primary': user.role === 'Manufacturer',
                            'text-warning': user.role === 'Customer',
                            'text-danger': user.role === 'Super Admin',
                          }">
                        @{{ user.role }}
                    </span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Contacts -->

                </div>
            </div>
            <!-- /Chat contacts -->

            <!-- Chat History -->
            <div class="col app-chat-history bg-body" v-cloak>
                <div class="chat-history-wrapper">
                    <div class="chat-history-header border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex overflow-hidden align-items-center">
                                <i class="ti ti-menu-2 ti-sm cursor-pointer d-lg-none d-block me-2"
                                   data-bs-toggle="sidebar" data-overlay data-target="#app-chat-contacts"></i>
                                <div class="flex-shrink-0 avatar "
                                     :class="[ isOnline(selectedUser.id)?'avatar-online':'avatar-offline' ]">
                                    <img :src="selectedUser?.avatar" alt="Avatar" class="rounded-circle"
                                         data-bs-toggle="sidebar" data-overlay data-target="#app-chat-sidebar-right">
                                </div>
                                <div class="chat-contact-info flex-grow-1 ms-2">
                                    <h6 class="m-0">@{{selectedUser?.name}}</h6>
                                    <small class="user-status text-muted">@{{selectedUser?.role}}</small>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="chat-history-body bg-body d-flex flex-column" ref="chatHistory">
                        <ul class="list-unstyled chat-history">
                            <li v-for="message in messages" :key="message.id"
                                :class="{ 'chat-message-right': message.from_id === currentUserId }"
                                class="chat-message ">
                                <div class="d-flex overflow-hidden">
                                    <div class="user-avatar flex-shrink-0 me-3"
                                         v-if=" message.from_id !== currentUserId">
                                        <div class="avatar avatar-sm">
                                            <img :src="selectedUser.avatar" alt="Avatar"
                                                 class="rounded-circle">
                                        </div>
                                    </div>
                                    <div class="chat-message-wrapper flex-grow-1">
                                        <div class="chat-message-text" style="max-width:700px">
                                            <div v-if="message.file" class="file-wrapper"
                                                 style="position: relative; display: inline-block;">

                                                <!-- IMAGE FILE -->
                                                <div v-if="/\.(jpg|jpeg|png|gif|webp)$/i.test(message.file)"
                                                     style="position: relative; display: inline-block;">
                                                    <img :src="message.file" width="100px" height="100px"
                                                         class="rounded border">

                                                    <!-- Floating download icon -->
                                                    <a :href="message.file" download target="_blank"
                                                       style="position: absolute; top: 5px; right: 5px;
                                                      background: rgba(0,0,0,0.6); color: #fff;
                                                      padding: 5px; border-radius: 50%;
                                                      display: flex; align-items: center; justify-content: center;">
                                                        <i class="ti ti-download"></i>
                                                    </a>
                                                </div>

                                                <!-- NON-IMAGE FILES (PDF, XLS, DOC, etc.) -->
                                                <div v-else
                                                     style="display: flex; align-items: center; gap: 8px; border: 1px solid #ddd; padding: 6px 10px; border-radius: 6px; background: #000000;">
                                                    <!-- File icon -->
                                                    <i v-if="/\.pdf$/i.test(message.file)"
                                                       class="ti ti-file-description text-danger"></i>
                                                    <i v-else-if="/\.(xls|xlsx)$/i.test(message.file)"
                                                       class="ti ti-file-spreadsheet text-success"></i>
                                                    <i v-else class="ti ti-file text-primary"></i>

                                                    <!-- File name -->
                                                    <span
                                                        style="max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                            @{{ message.file.split('/').pop() }}
                                                        </span>

                                                    <!-- Download icon -->
                                                    <a :href="message.file" download target="_blank"
                                                       style="margin-left: auto; background: rgba(0,0,0,0.6); color: #fff;
                  padding: 5px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <i class="ti ti-download"></i>
                                                    </a>
                                                </div>

                                            </div>

                                            <!-- Message text -->
                                            <p class="mb-0" v-if="message.message">@{{ message.message }}</p>
                                        </div>

                                        <div class="text-end text-muted mt-1">
                                            <small>@{{ message.created_at }}</small>
                                        </div>
                                    </div>
                                    <div class="user-avatar flex-shrink-0 ms-3"
                                         v-if=" message.from_id === currentUserId">
                                        <div class="avatar avatar-sm">
                                            <img :src="currentUserAvatar" alt="Avatar"
                                                 class="rounded-circle">
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <!-- Preview inside your chat-history-body -->
                        <div v-if="file"
                             class="mt-auto d-flex justify-content-between align-items-center w-100 p-2 border rounded"
                             style="height: 50px;">
                            <div>
                                <img v-if="isImage(file)" :src="getFileURL(file)"
                                     style="max-width:50px; max-height:50px;"/>
                                <iframe v-else-if="isPDF(file)" :src="getFileURL(file)" width="50" height="50"></iframe>
                                <p v-else>@{{ file.name }}</p>
                            </div>
                            <div @click="removeSelectedFile"><i class="ti ti-square-rounded-x text-danger"></i></div>
                        </div>
                    </div>
                    <!-- Chat message form -->
                    <div class="chat-history-footer shadow-sm">
                        <div class="form-send-message d-flex justify-content-between align-items-center ">
                            <input class="form-control message-input border-0 me-3 shadow-none"
                                   v-on:keyup.enter="sendMessage" v-model="newMessage"
                                   placeholder="Type your message here">

                            <div class="message-actions d-flex align-items-center">

                                <div class="position-relative">
                                    <i class="ti ti-mood-smile ti-sm cursor-pointer mx-1"
                                       @click="showEmojiPicker = !showEmojiPicker"></i>

                                    <!-- Emoji Picker -->
                                    <div v-if="showEmojiPicker" class="emoji-picker-container"
                                         style="position: absolute; bottom: 40px; right: 0; z-index: 1000;">
                                        <emoji-picker @emoji-click="addEmoji"></emoji-picker>
                                    </div>
                                </div>

                                <label for="attach-doc" class="form-label mb-0">
                                    <i class="ti ti-photo ti-sm cursor-pointer mx-3"></i>
                                    <input type="file" id="attach-doc" @change="onFileChanged($event)" hidden>
                                </label>
                                <button class="btn btn-primary d-flex send-msg-btn" type="button"
                                        v-on:click="sendMessage">
                                    <i class="ti ti-send me-md-1 me-0"></i>
                                    <span class="align-middle d-md-inline-block d-none">Send</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Chat History -->


            <div class="app-overlay"></div>
        </div>
    </div>

@endsection


@push('scripts')

    <script>
        window.authUser = {
            id: {{ auth()->id() }},
            name: '{{auth()->user()->name}}',
            avatar: '{{getFilePath(auth()->user()->avatar)}}',
        };
        window.directChatUser = @json(request()->query('directChatUser') ?? null);
        window.presetChatText = @json(request()->query('presetChatText') ?? null);
    </script>
    <script src="{{ asset(mix('js/chat/chat.js')) }}"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

@endpush
