# 💬 Chat Module — Complete Feature Documentation

> **Purpose:** This document explains every feature of the Chat module end-to-end so you can rebuild the exact same application in another project. It covers the backend architecture, frontend components, real-time socket logic, database schemas, and all business rules.

---

## 📦 Tech Stack

| Layer | Technology |
|---|---|
| **Backend Runtime** | Node.js (≥ 18) + Express.js |
| **Database** | MongoDB (via Mongoose) |
| **Real-time** | Socket.IO v4 |
| **Frontend** | React (Vite) |
| **State Management** | React Context API (`ChatContext`) |
| **Authentication** | JWT (JSON Web Tokens) |
| **File Storage** | Local disk (`/uploads/chat/`) via Multer |
| **UI Icons** | Lucide React |
| **Notifications** | react-hot-toast |
| **Date Formatting** | date-fns |

---

## 🗂️ Project Structure

```
backend/
  src/
    models/Chat/
      Conversation.js       ← MongoDB schema for conversations
      Message.js            ← MongoDB schema for messages
      Notification.js       ← MongoDB schema for chat notifications
    controllers/Chat/
      conversation.controller.js   ← REST API handlers for conversations
      message.controller.js        ← REST API handlers for messages
      upload.controller.js         ← File upload handler (Multer)
      user.controller.js           ← User search + online users
    services/
      conversation.service.js      ← Business logic for conversations
      message.service.js           ← Business logic for messages
    routes/chat/
      chat.routes.js               ← All /api/chat/* routes
    socket/
      socketHandler.js             ← Socket.IO event handling

frontend/
  src/
    context/
      ChatContext.jsx              ← Global chat state + socket connection
      AuthContext.jsx              ← Authentication state
    components/Chat/
      ChatLayout.jsx               ← Two-panel layout wrapper
      ConversationList.jsx         ← Left sidebar (list of chats/groups)
      ChatPanel.jsx                ← Right panel (messages + header)
      MessageInput.jsx             ← Typing area + file upload
      MessageItem.jsx              ← Individual message bubble
      TypingIndicator.jsx          ← Animated "... typing" indicator
      UserSearch.jsx               ← Search + select a user for DM
      CreateGroupModal.jsx         ← 2-step modal: name → add members
      GroupInfoModal.jsx           ← Group details + member management
      Chat.css                     ← All chat-specific styles
    services/
      chatService.js               ← Axios API calls (REST)
```

---

## 🔐 Authentication Flow

All chat API routes and socket connections are **JWT-protected**.

### REST API Auth
```
Every request must include:
Authorization: Bearer <token>

Middleware: auth.middleware.js
  - Decodes JWT
  - Attaches req.user to the request
```

### Socket Auth
```javascript
// Client connects with token in handshake
const socket = io(SOCKET_URL, {
  auth: { token: localStorage.getItem('token') }
});

// Server (socketHandler.js) verifies on every connection
const token = socket.handshake.auth.token;
decoded = authConfig.verifyToken(token);  // throws if invalid
const user = await User.findById(decoded.id);
```
If no token or invalid token → socket is immediately disconnected.

---

## 🗄️ Database Schemas

### 1. Conversation Schema (`Conversation.js`)

```javascript
{
  type: 'direct' | 'group' | 'department',   // Conversation type
  name: String,          // Only for group/department
  description: String,   // Optional group description
  avatar: String,        // Group avatar URL (optional)
  participants: [{
    userId: ObjectId,    // Reference to User model
    role: 'admin' | 'member',   // Group role (admin can manage)
    joinedAt: Date,
    lastReadMessageId: ObjectId, // For unread tracking
    unreadCount: Number,
    isMuted: Boolean,
    mutedUntil: Date
  }],
  createdBy: ObjectId,   // Who created this conversation
  isActive: Boolean,     // Soft-delete flag
  metadata: {
    department: String,
    isPinned: Boolean,
    tags: [String]
  },
  lastMessageAt: Date    // Indexed for sorting conversations
}
```

**Key indexes:**
- `participants.userId + lastMessageAt` — fast lookup for user's chats
- `type + isActive` — filter by type
- `metadata.department` — department chat lookup

**Instance methods:**
- `isParticipant(userId)` — check if user is in conversation
- `getParticipant(userId)` — find participant record
- `addParticipant(userId, role)` — add without duplicates
- `removeParticipant(userId)` — filter out participant
- `findOrCreateDirect(user1Id, user2Id)` (static) — find or create 1:1 conversation

---

### 2. Message Schema (`Message.js`)

```javascript
{
  conversationId: ObjectId,   // Parent conversation
  senderId: ObjectId,         // Who sent the message
  messageType: 'text' | 'file' | 'image' | 'system',
  content: String,            // Message text
  attachments: [{
    fileUrl: String,          // Served path or CDN URL
    fileName: String,
    fileType: String,         // 'image', 'pdf', 'doc', 'xlsx', 'file'
    fileSize: Number,         // Bytes
    thumbnailUrl: String      // For images
  }],
  replyTo: ObjectId,          // Reference to another message (reply)
  mentions: [ObjectId],       // @mentioned users
  reactions: [{
    userId: ObjectId,
    emoji: String,
    createdAt: Date
  }],
  status: {
    sent: Boolean,
    delivered: [{ userId, deliveredAt }],
    read: [{ userId, readAt }]
  },
  isEdited: Boolean,
  editedAt: Date,
  isDeleted: Boolean,         // Soft delete
  deletedAt: Date,
  deletedBy: ObjectId
}
```

**Key indexes:**
- `conversationId + createdAt` — paginated message loading
- `senderId + createdAt` — user's sent messages
- `mentions` — @mention lookup
- `conversationId + isDeleted + createdAt` — filtered message count

**Instance methods:**
- `isDeliveredTo(userId)`, `isReadBy(userId)`
- `markDelivered(userId)`, `markRead(userId)`
- `addReaction(userId, emoji)`, `removeReaction(userId, emoji)`

---

### 3. Chat Notification Schema (`Notification.js`)

```javascript
{
  userId: ObjectId,        // Who receives the notification
  type: 'new_message' | 'mention' | 'group_invite' | 'group_update',
  conversationId: ObjectId,
  messageId: ObjectId,
  senderId: ObjectId,
  content: String,
  isRead: Boolean,
  readAt: Date
}
```

**Static methods:**
- `getUnreadCount(userId)` — count unread notifications
- `markAllAsRead(userId)` — bulk mark as read
- `createNotification(data)` — create one notification

---

## 🌐 REST API Reference

**Base URL:** `POST /api/chat/*` (all routes require JWT)

### User Routes
```
GET  /api/chat/users              → Get all users (for group/DM search)
GET  /api/chat/users/search?q=   → Search users by name/email
GET  /api/chat/users/online       → Get list of online user IDs
```

### Conversation Routes
```
GET    /api/chat/conversations              → Get all conversations for logged-in user
POST   /api/chat/conversations             → Create conversation (direct or group)
GET    /api/chat/conversations/:id          → Get single conversation by ID
PUT    /api/chat/conversations/:id          → Update group (name, description, avatar)  [Admin only]
DELETE /api/chat/conversations/:id          → Delete/deactivate conversation            [Admin only]
POST   /api/chat/conversations/:id/participants         → Add members to group          [Admin only]
DELETE /api/chat/conversations/:id/participants/:userId → Remove member from group      [Admin only]
PUT    /api/chat/conversations/:id/participants/:userId/admin → Promote member to admin [Admin only]
PUT    /api/chat/conversations/:id/read                → Mark conversation as read
GET    /api/chat/conversations/search?q=               → Search conversations by name
```

### Message Routes
```
GET    /api/chat/conversations/:id/messages            → Get messages (paginated)
POST   /api/chat/conversations/:id/messages            → Send a message
PUT    /api/chat/messages/:id                          → Edit message (sender only)
DELETE /api/chat/messages/:id                          → Soft-delete message (sender only)
POST   /api/chat/messages/:id/reactions                → Add emoji reaction
DELETE /api/chat/messages/:id/reactions/:emoji         → Remove emoji reaction
GET    /api/chat/conversations/:id/messages/search?q=  → Search messages in conversation
PUT    /api/chat/conversations/:id/messages/read-all   → Mark all messages as read
```

### File Upload Route
```
POST /api/chat/upload    → Upload a file (image/PDF/doc/xls/txt, max 10MB)
```
Response:
```json
{
  "fileUrl": "/uploads/chat/filename.ext",
  "fileName": "original-name.pdf",
  "fileType": "pdf",
  "fileSize": 204800,
  "thumbnailUrl": null
}
```

---

## ⚡ Socket.IO Events Reference

### Connection Setup
```javascript
// Client initiates connection (ChatContext.jsx)
const socket = io(SOCKET_URL, {
  auth: { token: localStorage.getItem('token') },
  transports: ['websocket', 'polling']
});
```

### Events the Server EMITS to clients

| Event | Payload | When |
|---|---|---|
| `users_online` | `[userId, ...]` | When any user connects or disconnects |
| `new_message` | `{ message }` | When a message is sent to a room |
| `message_updated` | `{ message }` | When a message is edited |
| `message_deleted` | `{ messageId }` | When a message is deleted |
| `user_typing` | `{ conversationId, userId, userName }` | When a user starts typing |
| `user_stop_typing` | `{ conversationId, userId }` | When a user stops typing |
| `reaction_added` | `{ messageId, userId, emoji, reactions }` | When emoji is added |
| `reaction_removed` | `{ messageId, userId, emoji, reactions }` | When emoji is removed |
| `added_to_conversation` | `{ conversation, addedBy }` | When user is added to group |
| `removed_from_conversation` | `{ conversationId, removedBy }` | When user is removed |
| `conversation_updated` | `{ conversationId, updates }` | When group info changes |
| `new_notification` | `{ title, message, type, link }` | Push notification |

### Events the CLIENT EMITS to server

| Event | Payload | What it does |
|---|---|---|
| `join_conversation` | `conversationId` | Joins socket room for that chat |
| `leave_conversation` | `conversationId` | Leaves socket room |
| `typing` | `{ conversationId }` | Broadcasts typing status to room |
| `stop_typing` | `{ conversationId }` | Stops typing broadcast |

### Online Users Tracking (Server)
```javascript
// Server uses an in-memory Map
const onlineUsers = new Map(); // userId -> Set(socketIds)

// On connect: add to map, broadcast all online users
// On disconnect: remove socketId; if no sockets left → remove user, broadcast again
```
This supports **multiple tabs** — a user stays online as long as at least one socket is active.

---

## 🔄 Complete Feature Walkthroughs

---

### Feature 1: Direct Message (DM) — 1:1 Chat

**Step-by-step flow:**

1. User clicks the **`+` (Plus)** button in the chat sidebar header.
2. `UserSearch` modal opens — fetches all users via `GET /api/chat/users`.
3. The list shows every user with their name, role, and profile picture.
4. Already-added users are shown as greyed-out ("Already Added").
5. User selects someone → `createDirectConversation(userId)` is called.
6. **Backend:** `POST /api/chat/conversations` with `{ type: 'direct', participantIds: [targetUserId] }`
7. `conversationService.createDirectConversation()`:
   - Validates both users exist in DB
   - Calls `Conversation.findOrCreateDirect(user1Id, user2Id)` — finds existing 1:1 or creates new one
   - Returns populated conversation (with user names, profile pictures)
8. Socket broadcasts `added_to_conversation` to the other user.
9. `fetchConversations()` refreshes the sidebar.
10. The DM appears in the **Direct Chats** tab.
11. User clicks the conversation → `fetchMessages(conversationId)` loads history.
12. Socket room (`join_conversation`) is joined on load.

---

### Feature 2: Group Chat

#### Creating a Group

1. User clicks the **Groups icon** (👥) in the sidebar header.
2. `CreateGroupModal` opens — **Step 1: Enter Group Name**
   - Shows a camera placeholder for avatar.
   - User types a group name.
   - Clicks **Next** (disabled if name is empty).
3. **Step 2: Select Members**
   - Fetches all users via `GET /api/chat/users`.
   - User can search by name or email.
   - Click to toggle selection (blue checkmark overlay).
   - At least 2 members must be selected (CREATE button disabled otherwise).
4. Clicks **Create Group** → calls `createGroupConversation({ name, participantIds, description })`.
5. **Backend:** `POST /api/chat/conversations` with `{ type: 'group', name, participantIds: [...] }`
6. `conversationService.createGroupConversation()`:
   - Validates: name required, at least 2 participants
   - **Creator is automatically added** to participants as `role: 'admin'`
   - All other members get `role: 'member'`
   - Creates the `Conversation` document
7. Socket emits `added_to_conversation` to all new members immediately.
8. Sidebar switches to **Groups** tab automatically.

#### Admin-Only Rules (enforced both frontend & backend)

| Action | Who can do it |
|---|---|
| Add new member to group | **Admin only** |
| Remove a member from group | **Admin only** (cannot remove another admin) |
| Promote member to admin | **Admin only** |
| Edit group name/description/avatar | **Admin only** |
| Delete the group | **Admin only** |

**How admin is determined:**
```javascript
// Backend checks every time:
const participant = conversation.getParticipant(userId);
if (participant.role !== 'admin') {
  throw new Error('Only admins can add participants');
}

// Frontend shows/hides buttons:
const myParticipant = conversation.participants.find(p => p.userId._id === user?._id);
const isAdmin = myParticipant?.role === 'admin';
// "Add Member" button only renders if isAdmin === true
```

---

### Feature 3: Group Info Modal

Accessible by clicking the **⋮ (More Options)** icon in the chat header (only for group chats).

**What it shows:**
- Group name and avatar (initial letter if no avatar)
- Member count ("X members")
- List of all participants with:
  - Profile picture or initial placeholder
  - Name + email
  - Blue **"Admin"** badge for admins
  - 🛡️ **Make Admin** button (shield icon) — visible to admins for non-admin members
  - 🗑️ **Remove** button (trash icon) — visible to admins, cannot remove another admin or self

**Adding a member (admin only):**
1. Click **"+ Add Member"** (only shown for admins).
2. `UserSearch` modal opens inside the group info panel.
3. Existing members are passed as `existingUsers` — shown as greyed-out.
4. Admin selects a new user → `chatService.addParticipants(conversationId, [userId])`
5. Backend verifies admin role, adds member as `role: 'member'`.
6. Socket emits `added_to_conversation` to the new member.
7. Group info refreshes.

**Removing a member:**
1. Admin clicks trash icon next to a member.
2. Confirmation dialog appears: "Are you sure you want to remove [name]?"
3. `chatService.removeParticipant(conversationId, userId)` is called.
4. Backend verifies: requester is admin AND target is not another admin.
5. Socket emits `removed_from_conversation` to the removed user.

**Promoting a member to admin:**
1. Admin clicks shield icon next to a member.
2. Confirmation dialog appears.
3. `chatService.makeGroupAdmin(conversationId, userId)` is called.
4. Backend sets `target.role = 'admin'`.
5. All group members receive `conversation_updated` socket event.

---

### Feature 4: Online Status (Green Dot)

**How it works end-to-end:**

1. **Server** maintains an in-memory `Map`: `onlineUsers = new Map()` — maps `userId → Set(socketIds)`.
2. On socket **connect**: user added to map → `io.emit('users_online', Array.from(onlineUsers.keys()))` broadcast to ALL clients.
3. On socket **disconnect**: that socketId removed from user's Set. If Set is empty → user removed from map → another broadcast.
4. **Client** (`ChatContext.jsx`): listens to `users_online` event → stores as `Set` in state: `setOnlineUsers(new Set(users))`.
5. **ConversationList.jsx** checks:
```javascript
const isUserOnline = (conversation) => {
  if (conversation.type === 'direct') {
    const otherParticipant = conversation.participants.find(
      p => p.userId && p.userId._id !== user?._id
    );
    return otherParticipant && onlineUsers.has(otherParticipant.userId._id);
  }
  return false; // Groups don't show online dot
};

// In JSX:
{isUserOnline(conversation) && (
  <span className="online-indicator"></span>
)}
```
6. The `online-indicator` is styled as a **small green circle** via CSS, positioned absolutely on the avatar's bottom-right corner.

> **Note:** Online status green dot only appears on **Direct Messages**, not on group chats.

---

### Feature 5: Sending Messages

**Text messages:**
1. User types in the textarea in `MessageInput.jsx`.
2. As they type, `emitTyping(conversationId)` is called.
3. After 3 seconds of inactivity, `emitStopTyping()` is auto-called.
4. Press **Enter** (without Shift) or click the **Send** button.
5. `sendMessage(conversationId, content, 'text', [])` called.
6. **Backend:** `POST /api/chat/conversations/:id/messages`
7. `messageService.sendMessage()`:
   - Verifies user is a participant
   - Creates `Message` document with `status.sent = true`
   - Calls `conversationService.incrementUnreadCount()` for all other participants
   - Populates sender info
8. Controller emits `new_message` to the socket room.
9. **All clients** in the room receive the message via socket.
10. Message is added to `messages` state (deduplication check prevents doubles).
11. Conversation list is refreshed (`fetchConversations()`).

**Multi-line messages:** Use **Shift+Enter** to add new lines (Enter alone sends).

---

### Feature 6: File & Image Sharing

1. User clicks the **📎 (Paperclip)** icon.
2. File picker opens. Accepted: `image/*`, `.pdf`, `.doc`, `.docx`, `.xls`, `.xlsx`, `.txt`
3. File is uploaded to `POST /api/chat/upload` (Multer, 10MB limit).
4. Stored in `/uploads/chat/` with a unique timestamp-based filename.
5. Response returns `{ fileUrl, fileName, fileType, fileSize, thumbnailUrl }`.
6. Preview shown above the input:
   - **Images:** thumbnail preview with ✕ remove button
   - **Files:** filename + paperclip icon with ✕ remove button
7. User can still type a text message alongside the file.
8. On send, `messageType` is determined:
   - `'image'` if fileType is image
   - `'file'` for everything else
9. The message is stored with `attachments` array.

**Message rendering (`MessageItem.jsx`):**
- **Images:** Inline `<img>` tag
- **PDFs/Docs:** File card with icon (red for PDF, blue for others) + filename + size + download link

---

### Feature 7: Typing Indicator

**When typing starts:**
1. `handleTyping` fires on every keystroke.
2. If `isTyping === false` and text length > 0: set `isTyping = true`, emit `typing` to server.
3. Server broadcasts `user_typing` to everyone in the conversation room **except** the sender.
4. Clients update `typingUsers` Map: `conversationId → Set(userName)`.

**When typing stops:**
1. A 3-second debounce timer fires after last keystroke.
2. `emitStopTyping()` called → server broadcasts `user_stop_typing`.
3. If user sends message, `emitStopTyping()` is called immediately.

**Display (`TypingIndicator.jsx`):**
```
[Avatar] [...] "John is typing"
         Animated 3-dot bubble
```
- 1 person: "John is typing"
- 2 people: "John and Jane are typing"
- 3+ people: "3 people are typing"

---

### Feature 8: Message Status (Sent / Delivered / Read)

Only visible on **your own messages** (sent bubble, right side).

| Icon | Meaning |
|---|---|
| Single ✓ (`Check`) | Sent to server |
| Double ✓✓ (`CheckCheck`, grey) | Delivered to recipient |
| Double ✓✓ (`CheckCheck`, blue) | Read by recipient |
| `...` | Sending (optimistic) |

**How delivery/read tracking works:**
- `status.delivered: [{ userId, deliveredAt }]`
- `status.read: [{ userId, readAt }]`
- When user opens a conversation → `markAllAsRead()` called → last message's ID stored in `participant.lastReadMessageId`
- Read status shown when `status.read.length > 0`

---

### Feature 9: Conversation List (Sidebar)

**Layout:**
```
[💬 Messages] [unreadBadge]    [👥] [+]
[──────────────────────────────────────]
[Direct Chats]  [Groups]   ← Tabs
[──────────────────────────────────────]
[🔍 Search...]
[──────────────────────────────────────]
[Avatar] [Name]          [time]
         [Last message...]    [unread count]
```

**Tabs:** Direct Chats | Groups — filter `conversations` array by `conv.type`.

**Search:** Filters locally already-loaded conversations:
- For direct chats: searches by other participant's name
- For groups: searches by group name

**Last message preview:**
- `📎 File` for file messages
- `🖼️ Image` for image messages
- Truncated to 40 characters for text messages

**Unread count badge:** Shows number of unread messages from `participant.unreadCount`. Cleared when the conversation is opened.

**Sorting:** Conversations sorted by `lastMessageAt` descending (most recent at top).

---

### Feature 10: Chat Panel (Right Side)

**Header:**
```
[Avatar] [Name / Group Name]
         [X members]   ← only for groups

                        [🔔 Nudge] [⋮ Options]
```
- **Nudge button:** Sends a `👋 Nudged you!` text message instantly
- **⋮ Options:** Opens `GroupInfoModal` (for group chats only)

**Messages area:**
- Messages grouped visually (consecutive messages from same sender are grouped)
- First message in a group: shows sender avatar + name
- Subsequent messages: no avatar, slightly different border radius
- Auto-scrolls to bottom: instant on initial load, smooth on new messages

**Message bubbles:**
- Your messages: right-aligned, colored background
- Others' messages: left-aligned, white/neutral background

---

### Feature 11: Emoji Reactions

1. User can react to any message with an emoji.
2. `POST /api/chat/messages/:id/reactions` with `{ emoji }`.
3. Server:
   - Removes any existing reaction from same user with same emoji (toggle behavior)
   - Adds the new reaction
4. Socket broadcasts `reaction_added` to entire conversation room.
5. All clients update their messages state instantly.
6. To remove: `DELETE /api/chat/messages/:id/reactions/:emoji`.
7. Socket broadcasts `reaction_removed`.

---

### Feature 12: Message Edit & Delete

**Edit:**
- Only the **sender** can edit their message.
- `PUT /api/chat/messages/:id` with `{ content: 'new text' }`
- Sets `isEdited = true` and `editedAt = now`
- Socket emits `message_updated` to all room members

**Delete:**
- Only the **sender** can delete their message (soft delete).
- `DELETE /api/chat/messages/:id`
- Sets `isDeleted = true`, `deletedAt`, `deletedBy`
- Messages are excluded from all future queries with `isDeleted: false` filter
- Socket emits `message_deleted` to all clients

---

### Feature 13: Search

**Search conversations:** `GET /api/chat/conversations/search?q=keyword`
- MySQL-style regex search on `name` and `description` fields
- Returns last 20 matches, sorted by `lastMessageAt`

**Search messages in a conversation:** `GET /api/chat/conversations/:id/messages/search?q=keyword`
- Case-insensitive regex search on message `content`
- Limited to 50 results, sorted newest first

---

### Feature 14: Push Notifications (In-App)

When a socket event `new_notification` is received, the client shows a styled toast notification:
```javascript
{
  title: 'New Message from John',
  message: 'Hello, are you available?',
  type: 'success' | 'error' | 'info',
  link: '/chat'
}
```
Displayed via `react-hot-toast` with:
- Custom styling (rounded, left colored border)
- 6-second duration
- Clickable — redirects to `notification.link`

---

### Feature 15: Nudge Feature

A quick way to get someone's attention:
1. Click the 🔔 Bell icon in the chat header.
2. Automatically sends `👋 Nudged you!` as a text message in the current conversation.
3. Uses the same `sendMessage()` flow — other user sees it in real-time.

---

## 🔄 ChatContext State Management

The `ChatContext.jsx` is the single source of truth for all chat state.

**Exposed state:**
```javascript
{
  socket,                    // Socket.IO instance
  connected,                 // Boolean: socket connected?
  conversations,             // Array of all user's conversations
  totalUnreadCount,          // Sum of all conversation unread counts
  activeConversation,        // Currently selected conversation object
  setActiveConversation,     // Setter for active conversation
  messages,                  // Array of messages for active conversation
  typingUsers,               // Map: conversationId → Set(userName)
  onlineUsers,               // Set of online userIds
  loading,                   // Boolean: API loading state
  error,                     // Last error message

  // Functions
  fetchConversations,
  fetchMessages,
  sendMessage,
  createConversation,
  createDirectConversation,
  createGroupConversation,
  deleteConversation,
  markAsRead,
  emitTyping,
  emitStopTyping
}
```

**Key behaviors:**
- Socket connection initialized once when `user` is available.
- All conversations are automatically joined as socket rooms on connect.
- When active conversation changes → messages are fetched + conversation marked as read.
- `uploadFile` in `ChatContext` calls `POST /api/chat/upload` and returns the file metadata.

---

## 🎨 CSS Classes Reference (Chat.css)

| Class | Component |
|---|---|
| `.conversation-list` | Left sidebar wrapper |
| `.conversation-header` | Header with title + buttons |
| `.conversation-tabs` | Tabs row (Direct/Groups) |
| `.tab-btn.active` | Active tab styling |
| `.conversation-search` | Search bar wrapper |
| `.conversation-item.active` | Selected conversation highlight |
| `.online-indicator` | 🟢 Green dot for online status |
| `.unread-badge` | Red badge in header for total unread |
| `.unread-count` | Per-conversation unread bubble |
| `.chat-panel-header` | Right panel header |
| `.message-item.sent` | Right-aligned sent message |
| `.message-item.received` | Left-aligned received message |
| `.message-bubble.first` | First in group (rounded top) |
| `.message-bubble.last` | Last in group (rounded bottom) |
| `.typing-indicator` | Typing bubble wrapper |
| `.typing-dots span` | Animated bouncing dots |
| `.status-icon.read` | Blue double-check |
| `.status-icon.delivered` | Grey double-check |
| `.user-search-modal` | Full-screen modal overlay |
| `.loading-spinner` | Circular loading animation |
| `.mini-spinner` | Smaller spinner for inline use |

---

## ⚙️ Environment Variables

**Backend (`.env`):**
```env
PORT=5000
MONGO_URI=mongodb+srv://<user>:<pass>@cluster.mongodb.net/<dbname>
JWT_SECRET=your_jwt_secret_key
```

**Frontend (`.env` in Vite project):**
```env
VITE_API_URL=http://localhost:5000/api
VITE_SOCKET_URL=http://localhost:5000
```

> If `VITE_SOCKET_URL` is not set, the frontend auto-detects: localhost → `http://localhost:5000`, otherwise → production URL.

---

## 🚀 How to Run

### Backend
```bash
cd HRM/backend
npm install
npm run dev      # nodemon server.js on port 5000
```

### Frontend
```bash
cd HRM/frontend/frontend
npm install
npm run dev      # Vite dev server (usually port 5173)
```

---

## 📝 Business Rules Summary

| Rule | Where enforced |
|---|---|
| JWT required for all API calls | `auth.middleware.js` |
| JWT required for socket connection | `socketHandler.js` on connect |
| Only participants can see conversation | `conversationService.getConversationById()` |
| Only admins can add/remove members | `conversationService.addParticipants()` etc. |
| Cannot remove another admin | `GroupInfoModal.jsx` frontend + backend check |
| Creator of group = automatic admin | `createGroupConversation()` sets `role: 'admin'` |
| Only sender can edit/delete their message | `messageService.updateMessage()` / `deleteMessage()` |
| Direct conversation: exactly 1 other participant | `createConversation()` controller validates |
| Group requires name + minimum 2 members | `createGroupConversation()` service validates |
| File upload max 10MB | Multer `limits.fileSize` |
| Accepted file types | Multer `fileFilter` (images, PDFs, Office docs, txt) |
| Messages soft-deleted (never removed from DB) | `isDeleted: true` flag |
| Conversations soft-deleted | `isActive: false` flag |
| Duplicate direct messages prevented | `findOrCreateDirect()` static method |
| Typing broadcast excludes sender | `socket.to(room).emit()` (not `io.to()`) |
| Online status supports multiple tabs | Per-user Set of socketIds in server memory |

---

*This README was auto-generated by analyzing the full chat module source code. Use it as a blueprint to rebuild the exact same system.*
