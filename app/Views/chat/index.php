<?= view('layouts/header', ['title' => $title]) ?>

<div class="chat-inner-wrapper rounded-3xl overflow-hidden border border-slate-200 bg-white shadow-sm flex" style="height: calc(100vh - 160px);">
    <!-- Chat Sidebar (Conversation List) -->
    <aside class="chat-sidebar w-80 flex flex-col border-r border-slate-100">
        <div class="p-5 bg-white flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-800">Messages</h2>
            <button id="new-chat-btn" class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="New Chat">
                <i class="fas fa-plus"></i>
            </button>
        </div>
        
        <div class="px-5 pb-4 bg-white border-b border-slate-50">
            <div class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                <input type="text" placeholder="Search conversations..." class="w-full pl-11 pr-4 py-2.5 bg-slate-100 border-2 border-transparent rounded-2xl text-sm focus:bg-white focus:border-indigo-100 focus:ring-4 focus:ring-indigo-500/5 transition-all outline-none">
            </div>
        </div>

        <div class="conversation-list flex-1 overflow-y-auto" id="conv-list">
            <!-- Loaded via JS -->
            <div class="flex flex-col items-center justify-center py-10 opacity-20">
                <i class="fas fa-circle-notch fa-spin text-2xl"></i>
            </div>
        </div>
    </aside>

    <!-- Main Chat Panel -->
    <main class="chat-panel flex-1 flex flex-col bg-slate-50 relative" id="chat-panel">
        <!-- No Chat Selected Placeholder -->
        <div id="no-chat-selected" class="flex-1 flex flex-col items-center justify-center text-slate-400">
            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-4">
                <i class="fas fa-paper-plane text-2xl text-indigo-400"></i>
            </div>
            <h3 class="font-bold text-slate-800 mb-1">Select a conversation</h3>
            <p class="text-xs">Choose a contact or group to start messaging</p>
        </div>

        <!-- Active Chat Area -->
        <div id="active-chat" class="hidden flex-1 flex flex-col overflow-hidden h-full bg-white">
            <!-- Header -->
            <header class="h-16 px-4 md:px-6 border-b border-slate-100 flex items-center justify-between bg-white z-10 relative">
                <!-- Search Bar overlay -->
                <div id="search-container" class="hidden absolute inset-0 bg-white z-20 px-4 md:px-6 flex items-center gap-4 animate-in slide-in-from-top duration-200">
                    <i class="fas fa-search text-indigo-500"></i>
                    <input type="text" id="chat-search-input" placeholder="Search for messages..." class="flex-1 bg-transparent border-none outline-none text-sm text-slate-700 font-medium">
                    <button id="close-search" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:bg-slate-100 rounded-full transition-all mt-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="flex items-center gap-2 md:gap-3">
                    <button id="mobile-back-btn" class="md:hidden w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-100 rounded-full transition-all">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="w-10 h-10 rounded-full bg-slate-100 border-2 border-white shadow-sm flex items-center justify-center overflow-hidden" id="active-avatar-wrap">
                        <!-- Content updated via JS -->
                    </div>
                    <div>
                        <div class="font-bold text-slate-800 leading-tight" id="header-name">-</div>
                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400" id="header-status-wrap">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300" id="header-status-dot"></span>
                            <span id="header-status">offline</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-slate-400">
                    <button id="btn-gallery" class="hover:text-indigo-600 transition-colors" title="Shared Files"><i class="fas fa-images"></i></button>
                    <button id="btn-info" class="hover:text-indigo-600 transition-colors" title="Chat Info"><i class="fas fa-info-circle"></i></button>
                    <div class="w-px h-4 bg-slate-200"></div>
                    <button id="btn-search" class="hover:text-indigo-600 transition-colors"><i class="fas fa-search"></i></button>
                    <button id="chat-actions-btn" class="hover:text-indigo-600 transition-colors relative"><i class="fas fa-ellipsis-v"></i></button>
                </div>
            </header>

            <!-- Messages Area -->
            <div class="messages-area flex-1 p-6 overflow-y-auto bg-slate-50/50 space-y-4" id="messages-area">
                <!-- Messages JS -->
            </div>

            <!-- Footer / Input -->
            <footer class="p-4 bg-white border-t border-slate-100">
                <div id="attachment-preview" class="hidden mb-3 flex flex-wrap gap-2"></div>
                
                <!-- Emoji Picker -->
                <div id="emoji-picker" class="hidden absolute bottom-20 left-4 bg-white rounded-3xl shadow-2xl border border-slate-100 p-4 z-50 animate-in fade-in slide-in-from-bottom-2 duration-200">
                    <div class="grid grid-cols-3 gap-3">
                        <button class="emoji-item w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-50 transition-all text-xl text-yellow-400" data-emoji="😊"><i class="fas fa-smile"></i></button>
                        <button class="emoji-item w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-50 transition-all text-xl text-blue-400" data-emoji="😢"><i class="fas fa-sad-tear"></i></button>
                        <button class="emoji-item w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-50 transition-all text-xl text-orange-400" data-emoji="🙁"><i class="fas fa-frown"></i></button>
                        <button class="emoji-item w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-50 transition-all text-xl text-indigo-500" data-emoji="👍"><i class="fas fa-thumbs-up"></i></button>
                        <button class="emoji-item w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-50 transition-all text-xl text-red-500" data-emoji="👎"><i class="fas fa-thumbs-down"></i></button>
                        <button class="emoji-item w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-50 transition-all text-xl text-amber-500" data-emoji="👏"><i class="fas fa-hands-clapping"></i></button>
                    </div>
                </div>

                <input type="file" id="chat-file-input" class="hidden" multiple>
                <div class="flex items-center gap-3 bg-slate-100 p-2 pl-4 rounded-2xl border border-slate-200/50 focus-within:bg-white focus-within:border-indigo-500/30 focus-within:shadow-lg focus-within:shadow-indigo-500/5 transition-all">
                    <label for="chat-file-input" id="attach-btn" class="text-slate-400 hover:text-indigo-600 transition-colors cursor-pointer flex items-center justify-center">
                        <i class="fas fa-paperclip"></i>
                    </label>
                    <button id="emoji-btn" class="text-slate-400 hover:text-indigo-600 transition-colors"><i class="far fa-smile"></i></button>
                    <input type="text" id="chat-input" class="flex-1 bg-transparent border-none text-sm focus:ring-0 outline-none placeholder:text-slate-400 text-slate-700" placeholder="Type your message heartbeat...">
                    <button id="send-btn" class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 transition-all active:scale-95">
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </div>
            </footer>
        </div>
    </main>
</div>

<!-- Scripts -->
<script>
    const CURRENT_USER_ID = <?= session()->get('id') ?>;
    const ALL_USERS = <?= json_encode($allUsers) ?>;
    const API_URL = "<?= base_url('chat/api') ?>";
</script>
<script src="<?= base_url('js/chat.js?v=' . time()) ?>"></script>
<script>
    // Integration logic
    const originalOpenConversation = window.openConversation;
    window.openConversation = function(id) {
        $('#no-chat-selected').addClass('hidden');
        $('#active-chat').removeClass('hidden').addClass('flex flex-col');
        
        // Execute original logic
        if (typeof originalOpenConversation === 'function') {
            originalOpenConversation(id);
        }
        
        // Update header UI
        const conv = $(`.conversation-item[data-id="${id}"]`);
        if (conv.length) {
            $('#header-name').text(conv.find('.text-sm.font-bold').text());
            
            // Handle Direct (img) vs Group (i icon)
            const avatarHtml = conv.find('.avatar-container .w-11').html();
            $('#active-avatar-wrap').html(avatarHtml);
            
            const isOnline = conv.find('.bg-emerald-500').length > 0;
            if (isOnline) {
                $('#header-status').text('online').addClass('text-emerald-500').removeClass('text-slate-400');
                $('#header-status-dot').addClass('bg-emerald-500').removeClass('bg-slate-300');
            } else {
                $('#header-status').text('offline').removeClass('text-emerald-500').addClass('text-slate-400');
                $('#header-status-dot').removeClass('bg-emerald-500').addClass('bg-slate-300');
            }
        }
    };
</script>

<?= view('layouts/footer') ?>
