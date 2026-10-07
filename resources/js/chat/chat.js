const Vue = require('vue/dist/vue.js');
import echo from '../Echo';
Vue.config.devtools = false;
Vue.config.productionTip = false;
new Vue({
    el: "#chat-app",

    data: {
        recentChatUsers: [],
        allUsers: [],
        totalUsersCount: 0,
        selectedUser: null,
        file: '',
        newMessage: window.presetChatText ?? '',
        messages: [],
        onlineUsers: [],
        messageFromOtherUserIDs: [],
        currentUserId: window.authUser.id,
        currentUserName: window.authUser.name,
        currentUserAvatar: window.authUser.avatar,
        searchKeyWord: '',
        messageTone: null, // pagination variables
        allUsersPage: 1,
        allUsersLoading: false,
        allUsersHasMore: true,
        showEmojiPicker: false,
        timeout: null,
        activeChannels: {},
    }, methods: {
        sendMessage() {
            if (!this.selectedUser || (!this.newMessage && !this.file)) return;

            const formData = new FormData();
            formData.append('to_id', this.selectedUser.id);
            formData.append('message', this.newMessage);
            formData.append('from_id', this.currentUserId);
            if (this.file) {
                formData.append('attach_file', this.file);
            }
            fetch('/chat/send-message', {
                method: 'POST', headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }, body: formData
            });

            this.newMessage = '';
            this.file = '';
            this.showEmojiPicker = false;
        },
        addEmoji(emoji) {
            this.newMessage += emoji.detail.unicode;
        },
        onFileChanged(event) {
            if (event.target.files) {
                this.file = event.target.files[0];
            }
            if (this.file) {
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            }
        }, getFileURL(file) {
            return URL.createObjectURL(file);
        }, isImage(file) {
            return file && file.type.startsWith("image/");
        }, isPDF(file) {
            return file && file.type === "application/pdf";
        }, removeSelectedFile() {
            this.file = '';
        }, isOnline(userId) {
            return this.onlineUsers.some(u => u.id === userId);
        }, isMessageFromOtherUser(userID) {
            return this.messageFromOtherUserIDs.includes(userID);
        },
        listenEventPerUser(userID) {
            const ids = [this.currentUserId, userID].sort((a, b) => a - b);
            const channelName = `chat.${ids[0]}.${ids[1]}`;

            // Check if already listening to this channel
            if (this.activeChannels[channelName]) {
                return;
            }

            let privateMessage = echo;
            const channel = privateMessage.channel(channelName)
                .listen('.PrivateMessageSent', (e) => {
                    const index = this.recentChatUsers.findIndex(u => u.id === e.from.id || u.id === e.to.id);
                    if (index < 0) {
                        if (e.from.id === this.currentUserId) {
                            this.recentChatUsers.push(e.to)
                        } else {
                            this.recentChatUsers.push(e.from);
                        }
                    }

                    // Add null check here
                    if (!this.selectedUser) return;

                    if ((this.selectedUser.id === e.from.id || this.selectedUser.id === e.to.id) && this.selectedUser.id !== this.currentUserId) {
                        this.messages.push({
                            to_id: e.to.id,
                            message: e.message,
                            from_id: e.from.id,
                            file: e.file,
                            created_at: e.created_at,
                        });
                        if (this.selectedUser.id === e.from.id) this.messageTone.play();
                        // Scroll after new message
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                        return;
                    }
                    if (this.selectedUser.id !== e.from.id && this.currentUserId === e.to.id) {
                        if (!this.messageFromOtherUserIDs.includes(e.from?.id)) {
                            this.messageFromOtherUserIDs.push(e.from?.id);
                        }
                        this.messageTone.play();
                    }
                });
            this.activeChannels[channelName] = channel;
        },
        loadMessages() {
            if (!this.selectedUser) return;

            fetch(`/chat/messages/${this.selectedUser.id}`)
                .then(res => res.json())
                .then(messages => {
                    this.messages = messages.data.data.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                    // Scroll to bottom after messages are loaded
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                });
        },
        selectUser(user) {
            this.selectedUser = user;
            this.messages = [];
            const index = this.messageFromOtherUserIDs.indexOf(user.id);
            if (index !== -1) {
                this.messageFromOtherUserIDs.splice(index, 1);
            }
            this.loadMessages();
        }, fetchRecentUsers() {
            let url = `/chat/recent-users/${this.currentUserId}?search=${this.searchKeyWord}`;
            if (window.directChatUser) {
                url += `&directChatUser=${window.directChatUser}`;
            }
            fetch(url)
                .then(res => res.json())
                .then(response => {
                    this.recentChatUsers = response.data.data;
                    if (this.recentChatUsers.length > 0) {
                        this.selectUser(this.recentChatUsers[0]);
                    }
                    this.recentChatUsers.forEach((value) => {
                        this.listenEventPerUser(value.id);
                    });
                });
        },
        fetchAllUsers(page = 1) {
            if (this.allUsersLoading || !this.allUsersHasMore) return;

            this.allUsersLoading = true;
            fetch(`/chat/all-users/${this.currentUserId}?page=${page}&search=${this.searchKeyWord}`)
                .then(res => res.json())
                .then(response => {
                    let users = response.data.data;

                    if (page === 1) {
                        this.allUsers = users;
                    } else {
                        this.allUsers = [...this.allUsers, ...users];
                    }

                    this.totalUsersCount = response.data.total;

                    users.forEach((value) => {
                        this.listenEventPerUser(value.id);
                    });

                    this.allUsersPage = page;
                    this.allUsersHasMore = response.data.next_page_url !== null;

                    if (this.recentChatUsers.length < 1 && this.allUsers.length > 0) {
                        this.selectUser(this.allUsers[0]);
                    }
                })
                .finally(() => {
                    this.allUsersLoading = false;
                });
        },
        scrollToBottom() {
            const container = this.$refs.chatHistory;

            if (container) {
                // Use setTimeout to ensure DOM is fully updated
                setTimeout(() => {
                    container.scrollTop = container.scrollHeight;
                }, 100);
            }
        },
        handleSearch() {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.fetchRecentUsers();
                this.fetchAllUsers();
            }, 2000);
        },
        handleContactScroll() {
            const contactList = document.querySelector('#contact-list');
            if (contactList) {
                if (contactList.scrollTop + contactList.clientHeight >= contactList.scrollHeight - 10) {
                    this.fetchAllUsers(this.allUsersPage + 1);
                }
            }
        }
    },

    watch: {
        messages: {
            handler() {
                // Wait for DOM updates before scrolling
                this.$nextTick(() => {
                    setTimeout(() => {
                        this.scrollToBottom();
                    }, 100);
                });
            }, deep: true
        }, // Also watch selectedUser to ensure scroll on user change
        selectedUser: {
            handler(newUser) {
                if (newUser) {
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.scrollToBottom();
                        }, 150);
                    });
                }
            }
        }
    },

    created() {
        this.fetchRecentUsers();
        this.fetchAllUsers();
        this.messageTone = new Audio('./../../assets/audio/message-tone.mp3');
    },

    mounted() {
        document.getElementById('chat-loading').style.display = 'none';

        const onlineEvent = echo;
        onlineEvent.join('presence')
            .here(users => {
                this.onlineUsers = users;
            })
            .joining((user) => {
                if (!this.onlineUsers.find(u => u.id === user.id)) {
                    this.onlineUsers.push(user);
                }
            })
            .leaving((user) => {
                this.onlineUsers = this.onlineUsers.filter(u => u.id !== user.id);
            });

    }
});
