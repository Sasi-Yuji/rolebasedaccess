let activeConvId = null;
let lastMsgId = 0;
let pollTimer = null;
let pendingAttachments = [];

$(document).ready(function() {
    loadConversations();
    
    // Heartbeat every 30s
    setInterval(updateHeartbeat, 30000);
    updateHeartbeat();

    // Global poll every 5s for sidebar + delivery status
    setInterval(loadConversations, 5000);

    // Event Listeners
    $('#new-chat-btn').on('click', showNewChatModal);
    $('#conv-list').on('click', '.conversation-item', function() {
        const id = $(this).data('id');
        openConversation(id);
    });

    $('#send-btn').on('click', sendMessage);
    $('#chat-input').on('keypress', function(e) {
        if (e.which == 13) sendMessage();
    });

    $('#chat-file-input').on('change', handleFileUpload);

    $(document).on('click', '#chat-actions-btn', showChatActionsDropdown);
    $(document).on('click', '#btn-gallery', showGalleryModal);
    $(document).on('click', '#btn-info', showInfoModal);

    $(document).on('click', '#btn-search', () => {
        $('#search-container').removeClass('hidden').find('input').focus();
    });
    $(document).on('click', '#close-search', () => {
        $('#search-container').addClass('hidden');
        $('#chat-search-input').val('').trigger('input');
    });

    $('#sidebar-search').on('input', function() {
        const query = $(this).val().toLowerCase();
        $('.conversation-item').each(function() {
            const name = $(this).find('h4').text().toLowerCase();
            const lastMsg = $(this).find('p').text().toLowerCase();
            if (name.includes(query) || lastMsg.includes(query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $('#chat-search-input').on('input', function() {
        const query = $(this).val().toLowerCase();
        if (!query) {
            $('.message-item').removeClass('opacity-20 blur-[1px]').find('.message-content').each(function() {
                 $(this).html($(this).text()); // Reset highlights
            });
            return;
        }

        $('.message-item').each(function() {
            const $msgDiv = $(this).find('.message-content');
            if ($msgDiv.length === 0) return;
            
            const content = $msgDiv.text().toLowerCase();
            if (content.includes(query)) {
                $(this).removeClass('opacity-20 blur-[1px]');
                const rawText = $msgDiv.text();
                const regex = new RegExp(`(${query})`, 'gi');
                $msgDiv.html(rawText.replace(regex, '<span class="bg-yellow-200 text-slate-800 p-0.5 rounded">$1</span>'));
            } else {
                $(this).addClass('opacity-20 blur-[1px]');
            }
        });
    });
    $(document).on('click', '.dropdown-item', handleDropdownAction);
    $(document).on('click', '#emoji-btn', function(e) {
        e.stopPropagation();
        $('#emoji-picker').toggleClass('hidden');
    });

    $(document).on('click', '.emoji-item', function() {
        const emoji = $(this).data('emoji');
        const $input = $('#chat-input');
        $input.val($input.val() + emoji).focus();
        // Keep picker open or close? User said "if i click this it should show", usually you might want to add multiple. Let's keep it open for now but close on document click.
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#emoji-btn, #emoji-picker').length) {
            $('#emoji-picker').addClass('hidden');
        }
        if (!$(e.target).closest('#chat-actions-btn, .chat-dropdown').length) {
            $('.chat-dropdown').remove();
        }
        if (!$(e.target).closest('#btn-search, #search-container').length) {
             // Close search? No, user didn't ask.
        }
    });

    $(document).on('click', '#mobile-back-btn', function() {
        $('#chat-panel').removeClass('active-mobile');
    });

    $(document).on('click', '.user-item', function() {
        const userId = $(this).data('id');
        startDirectChat(userId);
    });

    $(document).on('click', '.modal-overlay', function(e) {
        if (e.target === this) $(this).remove();
    });
});

function showChatActionsDropdown() {
    $('.chat-dropdown').remove();
    const isGroup = activeConvType === 'group';
    let html = `
        <div class="chat-dropdown absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-200">
            <button class="dropdown-item w-full px-4 py-2 text-left text-sm hover:bg-slate-50 flex items-center gap-3 text-slate-600" data-action="clear-chat">
                <i class="far fa-trash-alt text-xs"></i> Clear Chat
            </button>
            <button class="dropdown-item w-full px-4 py-2 text-left text-sm hover:bg-slate-50 flex items-center gap-3 text-red-600 font-medium" data-action="delete-chat">
                <i class="fas fa-trash text-xs"></i> Delete Chat
            </button>
            ${isGroup ? `
                <div class="h-px bg-slate-100 my-1 mx-2"></div>
                <button class="dropdown-item w-full px-4 py-2 text-left text-sm hover:bg-slate-50 flex items-center gap-3 text-indigo-600 font-bold" data-action="manage-group">
                    <i class="fas fa-cog text-xs"></i> Manage Group
                </button>
            ` : ''}
        </div>
    `;
    $(this).append(html);
}

function handleDropdownAction() {
    const action = $(this).data('action');
    $('.chat-dropdown').remove();
    
    if (action === 'clear-chat') {
        showConfirmModal({
            title: 'Clear Chat',
            message: 'Are you sure you want to clear ALL messages? This cannot be undone.',
            btnText: 'Clear All',
            btnClass: 'bg-red-600',
            onConfirm: function() {
                $.post(`${API_URL}/conversations/${activeConvId}/clear`, function() {
                    $('#messages-area').empty();
                    loadConversations();
                });
            }
        });
    } else if (action === 'delete-chat') {
        showConfirmModal({
            title: 'Delete Conversation',
            message: 'This will remove the entire chat for everyone. Proceed?',
            btnText: 'Delete Now',
            btnClass: 'bg-red-600',
            onConfirm: function() {
                $.post(`${API_URL}/conversations/${activeConvId}/delete`, function() {
                    location.reload();
                });
            }
        });
    } else if (action === 'manage-group') {
        showManageGroupModal();
    }
}

function showManageGroupModal() {
    $.get(`${API_URL}/conversations/${activeConvId}/members`, function(members) {
        const isAdmin = members.find(m => m.user_id == CURRENT_USER_ID && m.chat_role === 'admin');
        
        let html = `
            <div class="modal-overlay fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
                <div class="modal bg-white w-full max-w-md rounded-3xl overflow-hidden shadow-2xl flex flex-col" style="max-height: 85vh;">
                    <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                        <h3 class="text-lg font-bold text-slate-800">Manage Group</h3>
                        <button class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition-colors" onclick="$('.modal-overlay').remove()">
                            <i class="fas fa-times text-slate-400"></i>
                        </button>
                    </div>

                    <div class="p-4 flex-1 overflow-y-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Members (${members.length})</h4>
                            ${isAdmin ? `<button class="text-[10px] font-bold text-indigo-600 hover:text-indigo-700 uppercase" onclick="showAddMembersModal()">+ Add Members</button>` : ''}
                        </div>
                        <div class="space-y-3">
                            ${members.map(m => `
                                <div class="flex items-center justify-between p-2 rounded-2xl hover:bg-slate-50">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 font-bold overflow-hidden">
                                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(m.name)}&background=random&color=fff" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800">${m.name} ${m.user_id == CURRENT_USER_ID ? '(You)' : ''}</div>
                                            <div class="text-[10px] ${m.chat_role === 'admin' ? 'text-indigo-600 font-bold' : 'text-slate-400'} uppercase">${m.chat_role}</div>
                                        </div>
                                    </div>
                                    ${isAdmin && m.user_id != CURRENT_USER_ID ? `
                                        <div class="flex gap-1">
                                            ${m.chat_role !== 'admin' ? `<button class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all" onclick="updateRole(${m.user_id}, 'admin')" title="Make Admin"><i class="fas fa-shield-alt text-[10px]"></i></button>` : ''}
                                            <button class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all" onclick="removeMember(${m.user_id})" title="Remove Member"><i class="fas fa-user-minus text-[10px]"></i></button>
                                        </div>
                                    ` : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('body').append(html);
    });
}

function updateRole(userId, role) {
    $.post(`${API_URL}/conversations/${activeConvId}/members/role`, { user_id: userId, role: role }, function() {
        $('.modal-overlay').remove();
        showManageGroupModal();
    });
}

function removeMember(userId) {
    showConfirmModal({
        title: 'Remove Member',
        message: 'Are you sure you want to remove this person from the group?',
        btnText: 'Remove',
        btnClass: 'bg-red-600',
        onConfirm: function() {
            $.post(`${API_URL}/conversations/${activeConvId}/members/remove`, { user_id: userId }, function() {
                $('.modal-overlay').remove();
                showManageGroupModal();
            });
        }
    });
}

function showAddMembersModal() {
    let html = `
        <div class="modal-overlay fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[110] flex items-center justify-center p-4">
            <div class="modal bg-white w-full max-w-xs rounded-3xl overflow-hidden shadow-2xl flex flex-col">
                <div class="p-5 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-800">Add to Group</h3>
                    <button onclick="$(this).closest('.modal-overlay').remove()"><i class="fas fa-times text-slate-400"></i></button>
                </div>
                <div class="p-4 max-h-60 overflow-y-auto">
                    ${ALL_USERS.map(u => `
                        <label class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox" class="add-participant-checkbox w-4 h-4 rounded border-slate-300" value="${u.id}">
                            <span class="text-sm text-slate-700">${u.name}</span>
                        </label>
                    `).join('')}
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100">
                    <button onclick="executeAddMembers()" class="w-full py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-xs shadow-md shadow-indigo-600/20 hover:bg-indigo-700">Add Selected</button>
                </div>
            </div>
        </div>
    `;
    $('body').append(html);
}

function executeAddMembers() {
    const ids = [];
    $('.add-participant-checkbox:checked').each(function() { ids.push($(this).val()); });
    if (ids.length === 0) return;
    
    $.post(`${API_URL}/conversations/${activeConvId}/members/add`, { participant_ids: ids }, function() {
        $('.modal-overlay').last().remove();
        $('.modal-overlay').last().remove();
        showManageGroupModal();
    });
}


function updateHeartbeat() {
    $.post(`${API_URL}/users/heartbeat`);
}

function loadConversations() {
    $.get(`${API_URL}/conversations`, function(data) {
        let html = '';
        data.forEach(conv => {
            // New logic: if we are loading the list, mark any received messages as delivered
            if (conv.last_message && conv.last_message.sender_id != CURRENT_USER_ID && !conv.last_message.delivered_at) {
                // Silently notify server that we've seen this in our list
                $.post(`${API_URL}/conversations/${conv.id}/deliver`);
            }

            html += `
                <div class="conversation-item ${activeConvId == conv.id ? 'active' : ''} ${conv.type === 'group' ? 'group-conv' : ''} flex items-center p-4 cursor-pointer hover:bg-slate-50 transition-all border-b border-slate-50 relative" data-id="${conv.id}">
                    <div class="avatar-container relative mr-3">
                        <div class="w-11 h-11 rounded-2xl ${conv.type === 'group' ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-600'} flex items-center justify-center font-bold text-sm overflow-hidden border-2 border-white shadow-sm">
                            ${conv.type === 'group' ? '<i class="fas fa-users"></i>' : `<img src="https://ui-avatars.com/api/?name=${encodeURIComponent(conv.display_name)}&background=random&color=fff" class="w-full h-full object-cover">`}
                        </div>
                        ${conv.is_online ? '<span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></span>' : ''}
                    </div>
                    <div class="flex-1 min-width-0">
                        <div class="flex justify-between items-center mb-0.5">
                            <h4 class="text-sm font-bold text-slate-800 truncate">${conv.display_name}</h4>
                            <span class="text-[10px] font-medium text-slate-400">${conv.last_message ? formatTime(conv.last_message.created_at) : ''}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-xs text-slate-500 truncate pr-2">
                                ${conv.last_message && conv.last_message.sender_id == CURRENT_USER_ID ? 
                                    `<span class="mr-1 shadow-none">${getStatusIcon(conv.last_message.status)}</span>` : ''}
                                ${conv.last_message ? conv.last_message.content : 'No messages yet'}
                            </p>
                            ${conv.unread_count > 0 ? `<span class="bg-pink-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center">${conv.unread_count}</span>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        $('#conv-list').html(html);
    });
}

function getStatusIcon(status) {
    if (status === 'read') return '<i class="fas fa-check-double text-[#34B7F1] text-[11px] font-bold"></i>';
    if (status === 'delivered') return '<i class="fas fa-check-double text-slate-500 text-[11px]"></i>';
    return '<i class="fas fa-check text-slate-500 text-[11px]"></i>';
}

let activeConvType = 'direct';

function openConversation(id) {
    activeConvId = id;
    const convItem = $(`.conversation-item[data-id="${id}"]`);
    activeConvType = convItem.hasClass('group-conv') ? 'group' : 'direct';

    // Mobile: Show chat panel
    $('#chat-panel').addClass('active-mobile');

    $('.conversation-item').removeClass('active');
    convItem.addClass('active');
    
    // Reset state
    lastMsgId = 0;
    pendingAttachments = [];
    renderAttachmentPreview();
    $('#messages-area').html('<div class="text-center p-5 opacity-50"><i class="fas fa-circle-notch fa-spin text-xl text-indigo-500"></i></div>');
    
    $.get(`${API_URL}/conversations/${id}/messages`, function(data) {
        $('#messages-area').empty();
        data.forEach(msg => appendMessage(msg));
        scrollToBottom();
        startPolling();
    });
}

function appendMessage(msg) {
    if ($(`.message-item[data-id="${msg.id}"]`).length > 0) return; // Prevent duplicates

    const isSent = msg.sender_id == CURRENT_USER_ID;
    const showSenderName = !isSent && activeConvType === 'group';

    let attachmentsHtml = '';
    if (msg.attachments && msg.attachments.length > 0) {
        attachmentsHtml = '<div class="message-attachments mt-2 mb-1 space-y-2">';
        msg.attachments.forEach(att => {
            const isImage = att.file_type.startsWith('image/');
            const isVideo = att.file_type.startsWith('video/');
            const fileName = att.file_name;
            const fileUrl = att.file_url;

            if (isImage) {
                attachmentsHtml += `
                    <div class="attachment-item image-attachment rounded-2xl overflow-hidden border border-slate-100 shadow-sm max-w-[240px]">
                        <a href="${fileUrl}" target="_blank">
                            <img src="${fileUrl}" class="w-full h-auto object-cover hover:scale-105 transition-transform duration-500">
                        </a>
                    </div>
                `;
            } else if (isVideo) {
                attachmentsHtml += `
                    <div class="attachment-item video-attachment rounded-2xl overflow-hidden border border-slate-100 shadow-sm max-w-[240px]">
                        <video src="${fileUrl}" controls class="w-full h-auto"></video>
                    </div>
                `;
            } else {
                // PDF, Doc, etc.
                let icon = 'fa-file';
                let color = 'text-slate-400';
                if (att.file_type.includes('pdf')) { icon = 'fa-file-pdf'; color = 'text-red-500'; }
                else if (att.file_type.includes('word') || att.file_type.includes('officedocument')) { icon = 'fa-file-word'; color = 'text-blue-500'; }
                else if (att.file_type.includes('sheet') || att.file_type.includes('excel')) { icon = 'fa-file-excel'; color = 'text-emerald-500'; }

                attachmentsHtml += `
                    <a href="${fileUrl}" target="_blank" class="attachment-item file-attachment flex items-center gap-3 p-3 bg-white/50 backdrop-blur-sm rounded-2xl border border-slate-100 hover:bg-white hover:shadow-md transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center ${color} text-lg group-hover:scale-110 transition-transform">
                            <i class="fas ${icon}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-slate-700 truncate">${fileName}</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">${(att.file_size / 1024).toFixed(1)} KB</div>
                        </div>
                        <i class="fas fa-arrow-down text-[10px] text-slate-300 group-hover:text-indigo-500"></i>
                    </a>
                `;
            }
        });
        attachmentsHtml += '</div>';
    }

    const html = `
        <div class="message-group ${isSent ? 'sent-group' : 'received-group'} flex flex-col mb-4 ${isSent ? 'items-end' : 'items-start'}">
            ${showSenderName ? `<span class="text-[10px] font-bold text-indigo-500 mb-1 ml-2">${msg.sender_name}</span>` : ''}
            <div class="message-item ${isSent ? 'sent' : 'received'} shadow-sm" data-id="${msg.id}">
                ${msg.content ? `<div class="message-content">${msg.content}</div>` : ''}
                ${attachmentsHtml}
            </div>
            <div class="message-meta flex items-center gap-1 mt-1 ${isSent ? 'mr-1' : 'ml-1'}">
                <span class="message-time text-[10px] font-bold text-slate-400">${formatTime(msg.created_at)}</span>
                ${isSent ? getStatusIcon(msg.status) : ''}
            </div>
        </div>
    `;
    $('#messages-area').append(html);
    if (parseInt(msg.id) > lastMsgId) lastMsgId = parseInt(msg.id);
}

function sendMessage() {
    const content = $('#chat-input').val().trim();
    if (!content && pendingAttachments.length === 0) return;
    if (!activeConvId) return;

    const data = { 
        content: content,
        attachments: pendingAttachments,
        message_type: pendingAttachments.length > 0 ? 'attachment' : 'text'
    };

    $('#chat-input').val('').focus();
    pendingAttachments = [];
    renderAttachmentPreview();

    $.post(`${API_URL}/conversations/${activeConvId}/messages/send`, data, function(msg) {
        appendMessage(msg);
        scrollToBottom();
        loadConversations();
    });
}

function handleFileUpload(e) {
    const files = e.target.files;
    if (files.length === 0) return;
    if (!activeConvId) {
        showToast('Please select a conversation first', 'warning');
        return;
    }

    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }

    const $attachBtn = $('#attach-btn');
    $attachBtn.html('<i class="fas fa-circle-notch fa-spin"></i>').addClass('pointer-events-none opacity-50');

    $.ajax({
        url: `${API_URL}/upload`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(attachments) {
            pendingAttachments = [...pendingAttachments, ...attachments];
            renderAttachmentPreview();
            $('#chat-file-input').val('');
        },
        error: function(xhr) {
            showToast('Upload failed', 'error');
        },
        complete: function() {
            $attachBtn.html('<i class="fas fa-paperclip"></i>').removeClass('pointer-events-none opacity-50');
        }
    });
}

function renderAttachmentPreview() {
    const $preview = $('#attachment-preview');
    if (pendingAttachments.length === 0) {
        $preview.empty().addClass('hidden');
        return;
    }

    let html = '';
    pendingAttachments.forEach((att, index) => {
        const isImage = att.file_type.startsWith('image/');
        html += `
            <div class="relative group bg-slate-100 p-2 rounded-xl flex items-center gap-2 border border-slate-200">
                ${isImage ? `
                    <img src="${att.file_url}" class="w-8 h-8 rounded-lg object-cover">
                ` : `
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fas fa-file-alt text-xs"></i>
                    </div>
                `}
                <span class="text-[10px] font-bold text-slate-600 max-w-[80px] truncate">${att.file_name}</span>
                <button onclick="removePendingAttachment(${index})" class="w-4 h-4 rounded-full bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all">
                    <i class="fas fa-times text-[8px]"></i>
                </button>
            </div>
        `;
    });
    $preview.html(html).removeClass('hidden');
}

function removePendingAttachment(index) {
    pendingAttachments.splice(index, 1);
    renderAttachmentPreview();
}

function startPolling() {
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(() => {
        if (!activeConvId) return;
        $.get(`${API_URL}/conversations/${activeConvId}/messages/poll?last_id=${lastMsgId}`, function(data) {
            // New format: { new_messages: [], other_last_read_id: X }
            const messages = data.new_messages || data; // fallback for safety
            if (messages.length > 0) {
                messages.forEach(msg => appendMessage(msg));
                scrollToBottom();
                loadConversations();
            }

            // Update ticks for sent messages based on other user's read status
            if (data.other_last_read_id) {
                $('.message-group.sent-group').each(function() {
                    const msgId = $(this).find('.message-item').data('id');
                    if (msgId <= data.other_last_read_id) {
                        $(this).find('.message-meta i').remove();
                        $(this).find('.message-meta').append(getStatusIcon('read'));
                    }
                });
            }
        });
    }, 2000);
}

function showNewChatModal() {
    let html = `
        <div class="modal-overlay fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
            <div class="modal bg-white w-full max-w-md rounded-3xl overflow-hidden shadow-2xl flex flex-col" style="max-height: 85vh;">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                    <h3 class="text-lg font-bold text-slate-800">New Chat</h3>
                    <button class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition-colors" onclick="$('.modal-overlay').remove()">
                        <i class="fas fa-times text-slate-400"></i>
                    </button>
                </div>
                
                <!-- Tabs -->
                <div class="flex border-b border-slate-50">
                    <button class="flex-1 py-4 text-sm font-bold border-b-2 border-indigo-600 text-indigo-600 chat-modal-tab" data-tab="direct">Direct Message</button>
                    <button class="flex-1 py-4 text-sm font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-600 chat-modal-tab" data-tab="group">Create Group</button>
                </div>

                <div class="modal-body flex-1 overflow-y-auto p-4">
                    <!-- Direct Tab Content -->
                    <div id="tab-direct" class="chat-tab-content">
                        <div class="space-y-1">
                            ${ALL_USERS.map(u => `
                                <div class="user-item flex items-center gap-3 p-3 rounded-2xl hover:bg-slate-50 cursor-pointer transition-colors" data-id="${u.id}">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold overflow-hidden">
                                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=random&color=fff" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-bold text-slate-700">${u.name}</div>
                                        <div class="text-[10px] text-slate-400 uppercase font-bold">${u.role}</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-slate-300 text-xs"></i>
                                </div>
                            `).join('')}
                        </div>
                    </div>

                    <!-- Group Tab Content -->
                    <div id="tab-group" class="chat-tab-content hidden">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Group Name</label>
                                <input type="text" id="group-name" placeholder="E.g. Engineering Team" class="w-full px-4 py-3 bg-slate-100 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Select Participants</label>
                                <div class="space-y-1 bg-slate-50/50 p-2 rounded-2xl border border-slate-100 max-h-60 overflow-y-auto">
                                    ${ALL_USERS.map(u => `
                                        <label class="flex items-center gap-3 p-2 rounded-xl hover:bg-white cursor-pointer transition-colors border border-transparent hover:border-slate-100">
                                            <input type="checkbox" class="participant-checkbox w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20" value="${u.id}">
                                            <div class="w-8 h-8 rounded-lg overflow-hidden flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=random&color=fff" class="w-full h-full object-cover">
                                            </div>
                                            <span class="text-sm font-medium text-slate-700">${u.name}</span>
                                        </label>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="group-footer" class="p-4 bg-slate-50 border-t border-slate-100 hidden">
                    <button id="btn-create-group" class="w-full py-3.5 bg-indigo-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-[0.98]">
                        Create Group
                    </button>
                </div>
            </div>
        </div>
    `;
    $('body').append(html);

    // Tab Logic
    $('.chat-modal-tab').on('click', function() {
        const tab = $(this).data('tab');
        $('.chat-modal-tab').removeClass('border-indigo-600 text-indigo-600').addClass('border-transparent text-slate-400');
        $(this).removeClass('border-transparent text-slate-400').addClass('border-indigo-600 text-indigo-600');
        
        $('.chat-tab-content').addClass('hidden');
        $(`#tab-${tab}`).removeClass('hidden');

        if (tab === 'group') {
            $('#group-footer').removeClass('hidden');
        } else {
            $('#group-footer').addClass('hidden');
        }
    });

    // Create Group Action
    $('#btn-create-group').on('click', createGroupAction);
}

function createGroupAction() {
    const name = $('#group-name').val().trim();
    const participants = [];
    $('.participant-checkbox:checked').each(function() {
        participants.push($(this).val());
    });

    if (!name) return showToast('Please enter group name', 'warning');
    if (participants.length < 1) return showToast('Select at least one participant', 'warning');

    $.post(`${API_URL}/conversations`, { 
        type: 'group', 
        name: name, 
        participant_ids: participants 
    }, function(data) {
        $('.modal-overlay').remove();
        loadConversations();
        openConversation(data.id);
        showToast('Group created successfully!');
    });
}

function startDirectChat(userId) {
    $.post(`${API_URL}/conversations`, { type: 'direct', user_id: userId }, function(data) {
        $('.modal-overlay').remove();
        loadConversations();
        openConversation(data.id);
    });
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function showConfirmModal(options) {
    let html = `
        <div class="modal-overlay fixed inset-0 bg-slate-900/40 backdrop-blur-md z-[200] flex items-center justify-center p-4 animate-in fade-in duration-300">
            <div class="modal bg-white w-full max-w-sm rounded-[32px] overflow-hidden shadow-2xl animate-in zoom-in-95 duration-300">
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-5 text-2xl">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">${options.title}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-8">${options.message}</p>
                    <div class="flex gap-3">
                        <button class="flex-1 py-3.5 bg-slate-100 text-slate-600 rounded-2xl font-bold text-sm hover:bg-slate-200 transition-all" onclick="$(this).closest('.modal-overlay').remove()">Cancel</button>
                        <button id="modal-confirm-btn" class="flex-1 py-3.5 ${options.btnClass || 'bg-indigo-600'} text-white rounded-2xl font-bold text-sm shadow-lg shadow-indigo-600/10 hover:opacity-90 transition-all">${options.btnText || 'Confirm'}</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('body').append(html);
    $('#modal-confirm-btn').on('click', function() {
        $(this).closest('.modal-overlay').remove();
        if (options.onConfirm) options.onConfirm();
    });
}

function showToast(message, type = 'success') {
    const bg = type === 'success' ? 'bg-emerald-500' : 'bg-red-500';
    let html = `
        <div class="fixed top-6 left-1/2 -translate-x-1/2 ${bg} text-white px-6 py-3 rounded-2xl font-bold text-sm shadow-2xl z-[300] animate-in slide-in-from-top-10 duration-500">
            ${message}
        </div>
    `;
    const $toast = $(html);
    $('body').append($toast);
    setTimeout(() => {
        $toast.addClass('animate-out fade-out slide-out-to-top-10').fadeOut(() => $toast.remove());
    }, 3000);
}

function showGalleryModal() {
    if (!activeConvId) return;

    $.get(`${API_URL}/conversations/${activeConvId}/attachments`, function(attachments) {
        let html = `
            <div class="modal-overlay fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[150] flex items-center justify-center p-4">
                <div class="modal bg-white w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl flex flex-col" style="max-height: 85vh;">
                    <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Shared Files</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${attachments.length} items found</p>
                        </div>
                        <button class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition-colors" onclick="$('.modal-overlay').remove()">
                            <i class="fas fa-times text-slate-400"></i>
                        </button>
                    </div>

                    <div class="p-6 overflow-y-auto bg-slate-50/50 flex-1">
                        ${attachments.length === 0 ? `
                            <div class="py-20 text-center">
                                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-slate-200 shadow-sm">
                                    <i class="fas fa-images text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-400">No media shared in this chat yet</p>
                            </div>
                        ` : `
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                ${attachments.map(att => {
                                    const isImage = att.file_type.startsWith('image/');
                                    const isVideo = att.file_type.startsWith('video/');
                                    
                                    if (isImage) {
                                        return `
                                            <div class="group relative aspect-square rounded-2xl overflow-hidden border border-white shadow-sm hover:shadow-xl transition-all duration-300">
                                                <img src="${att.file_url}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                                    <a href="${att.file_url}" target="_blank" class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md text-white flex items-center justify-center hover:bg-white hover:text-indigo-600 transition-all"><i class="fas fa-external-link-alt text-xs"></i></a>
                                                </div>
                                            </div>
                                        `;
                                    } else if (isVideo) {
                                         return `
                                            <div class="group relative aspect-square rounded-2xl overflow-hidden border border-white shadow-sm bg-slate-900 flex items-center justify-center">
                                                <video src="${att.file_url}" class="w-full h-full object-cover opacity-60"></video>
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <i class="fas fa-play text-white opacity-80 group-hover:scale-125 transition-transform"></i>
                                                </div>
                                                <a href="${att.file_url}" target="_blank" class="absolute inset-0"></a>
                                            </div>
                                        `;
                                    } else {
                                        let icon = 'fa-file';
                                        let color = 'text-slate-400';
                                        if (att.file_type.includes('pdf')) { icon = 'fa-file-pdf'; color = 'text-red-500'; }
                                        else if (att.file_type.includes('word')) { icon = 'fa-file-word'; color = 'text-blue-500'; }
                                        
                                        return `
                                            <a href="${att.file_url}" target="_blank" class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl border border-slate-100 hover:shadow-lg transition-all gap-2 text-center group">
                                                <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center ${color} text-xl group-hover:scale-110 transition-all">
                                                    <i class="fas ${icon}"></i>
                                                </div>
                                                <div class="text-[10px] font-bold text-slate-800 truncate w-full">${att.file_name}</div>
                                                <div class="text-[8px] font-bold text-slate-400 uppercase">${(att.file_size / 1024).toFixed(1)} KB</div>
                                            </a>
                                        `;
                                    }
                                }).join('')}
                            </div>
                        `}
                    </div>
                </div>
            </div>
        `;
        $('body').append(html);
    });
}

function showInfoModal() {
    if (!activeConvId) return;

    if (activeConvType === 'group') {
        showManageGroupModal();
    } else {
        // Direct chat info
        const conv = $(`.conversation-item[data-id="${activeConvId}"]`);
        const name = conv.find('h4').text();
        const isOnline = conv.find('.bg-emerald-500').length > 0;
        
        $.get(`${API_URL}/conversations/${activeConvId}/stats`, function(stats) {
            let html = `
                <div class="modal-overlay fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[150] flex items-center justify-center p-4">
                    <div class="modal bg-white w-full max-w-sm rounded-[40px] overflow-hidden shadow-2xl animate-in zoom-in-95 duration-300">
                        <div class="p-8 text-center bg-gradient-to-b from-indigo-50/50 to-white">
                            <div class="relative w-24 h-24 mx-auto mb-6">
                                <div class="w-full h-full rounded-[32px] bg-white p-1 shadow-xl">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=random&color=fff&size=128" class="w-full h-full object-cover rounded-[28px]">
                                </div>
                                ${isOnline ? '<span class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 border-4 border-white rounded-full"></span>' : ''}
                            </div>
                            
                            <h3 class="text-xl font-bold text-slate-800">${name}</h3>
                            <p class="text-sm font-medium text-slate-400 mb-6 uppercase tracking-widest">${isOnline ? 'Active Now' : 'Offline'}</p>
                            
                            <div class="grid grid-cols-3 gap-3">
                                <div class="p-3 rounded-3xl bg-white border border-slate-100 shadow-sm">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Messages</div>
                                    <div class="text-sm font-bold text-slate-800">${stats.total_messages}</div>
                                </div>
                                <div class="p-3 rounded-3xl bg-white border border-slate-100 shadow-sm">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Media</div>
                                    <div class="text-sm font-bold text-slate-800">${stats.total_media}</div>
                                </div>
                                <div class="p-3 rounded-3xl bg-white border border-slate-100 shadow-sm">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase mb-1">Mutual</div>
                                    <div class="text-sm font-bold text-slate-800">0</div>
                                </div>
                            </div>
                            
                            <div class="mt-8">
                                <button class="w-full py-4 bg-indigo-600 text-white rounded-3xl font-bold text-sm shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 transition-all flex items-center justify-center gap-2" onclick="$('.modal-overlay').remove()">
                                     Done
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(html);
        });
    }
}

function scrollToBottom() {
    const area = document.getElementById('messages-area');
    if (area) {
        area.scrollTop = area.scrollHeight;
    }
}
