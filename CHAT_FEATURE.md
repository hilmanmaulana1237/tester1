# Fitur Live Chat untuk Task Work Wizard

## 📋 Overview

Fitur live chat memungkinkan komunikasi real-time antara user dan admin langsung di dalam website ketika user sedang mengerjakan task di halaman `http://localhost:8000/user/task/{id}/work`.

## ✨ Fitur Utama

### 1. **Chat Widget untuk User**

-   ✅ Tombol chat yang dapat dibuka/ditutup (collapsible)
-   ✅ Badge notifikasi untuk pesan yang belum dibaca
-   ✅ Auto-refresh setiap 10 detik untuk pesan baru
-   ✅ Desain responsive untuk mobile dan desktop
-   ✅ Tampilan bubble chat yang modern
-   ✅ Status "Read/Unread" untuk setiap pesan
-   ✅ Avatar untuk membedakan admin dan user
-   ✅ Timestamp untuk setiap pesan
-   ✅ Character counter (max 1000 karakter)
-   ✅ Keyboard shortcut (Enter untuk kirim, Shift+Enter untuk baris baru)

### 2. **Admin Panel untuk Mengelola Chat**

-   ✅ Halaman khusus "Task Chats" di navigation menu Filament
-   ✅ Badge notifikasi untuk unread messages di navigation
-   ✅ Grouping chat berdasarkan User Task
-   ✅ Filter berdasarkan sender (User/Admin) dan status (Read/Unread)
-   ✅ Action "Reply" langsung dari table
-   ✅ Action "Mark as Read" untuk pesan user
-   ✅ Widget chat terintegrasi di halaman detail User Task
-   ✅ Auto-refresh setiap 10 detik
-   ✅ Admin hanya bisa melihat chat dari task yang dia buat (kecuali superadmin)

### 3. **Persistent Chat History**

-   ✅ Semua pesan tersimpan di database table `task_messages`
-   ✅ History chat tidak hilang saat refresh/reload
-   ✅ Relasi dengan `user_tasks` dan `users`
-   ✅ Timestamps untuk tracking kapan pesan dikirim dan dibaca

## 🗄️ Database Structure

### Table: `task_messages`

```sql
CREATE TABLE task_messages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_task_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    sender_type ENUM('user', 'admin') DEFAULT 'user',
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_task_id) REFERENCES user_tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_user_task_created (user_task_id, created_at),
    INDEX idx_sender_read (sender_type, is_read)
);
```

## 📁 File Structure

### Models

```
app/Models/
├── TaskMessage.php          # Model untuk chat messages
│   ├── Relations: userTask(), user()
│   ├── Scopes: unread(), forAdmin(), forUser()
│   └── Methods: markAsRead()
│
└── UserTask.php (updated)
    └── Relations: messages(), unreadMessagesForUser(), unreadMessagesForAdmin()
```

### Livewire Components

```
app/Livewire/
└── TaskChat.php             # Component untuk chat widget user
    ├── mount()
    ├── loadMessages()
    ├── sendMessage()
    ├── refreshMessages()
    └── markAdminMessagesAsRead()
```

### Filament Resources

```
app/Filament/Resources/
├── TaskChats/
│   ├── TaskChatResource.php      # Resource untuk manage chat
│   └── Pages/
│       └── ManageTaskChats.php   # Page untuk list & manage
│
└── UserTasks/
    └── Widgets/
        └── TaskChatWidget.php    # Widget chat di detail page
```

### Views

```
resources/views/
├── livewire/
│   ├── task-chat.blade.php           # UI chat untuk user
│   └── task-work-wizard.blade.php    # Updated dengan chat widget
│
└── filament/resources/user-tasks/widgets/
    └── task-chat-widget.blade.php    # UI chat untuk admin
```

### Migrations

```
database/migrations/
└── 2025_12_08_225220_create_task_messages_table.php
```

## 🎨 UI/UX Features

### User Side (Task Work Wizard)

1. **Collapsible Chat Button**

    - Gradient blue-indigo button
    - Badge dengan jumlah unread messages
    - Smooth expand/collapse animation

2. **Chat Interface**

    - Modern bubble design
    - Avatar circles dengan warna berbeda (admin = orange, user = blue)
    - Sender name dan timestamp
    - Read receipt (✓✓) untuk pesan yang sudah dibaca
    - Empty state dengan icon dan message

3. **Input Area**
    - Auto-expanding textarea
    - Character counter
    - Send button dengan loading state
    - Keyboard shortcuts tip

### Admin Side (Filament Panel)

1. **Navigation**

    - Menu item "Task Chats" dengan badge notifikasi
    - Icon chat bubble

2. **Table View**

    - Grouped by User Task
    - Columns: Task, User, From, Message, Status, Sent At, Read At
    - Filters: Sender Type, Read Status
    - Actions: View, Reply, Mark as Read

3. **Chat Widget (Detail Page)**
    - Integrated di footer halaman View User Task
    - Same UI as user side
    - Auto-mark user messages as read

## 🔄 Real-time Updates

### Polling Strategy

-   User side: Poll setiap 10 detik (`wire:poll.10s`)
-   Admin side: Poll setiap 10 detik (`wire:poll.10s`)
-   Auto-scroll to bottom saat ada pesan baru

### Event Broadcasting (Optional Enhancement)

Untuk implementasi masa depan, bisa menggunakan Laravel Echo + Pusher/Soketi untuk real-time tanpa polling.

## 🔐 Security & Permissions

### User Side

-   User hanya bisa chat di task yang mereka ambil
-   User hanya bisa melihat chat untuk `userTask` mereka sendiri

### Admin Side

-   **Superadmin**: Bisa melihat semua chat
-   **Admin biasa**: Hanya bisa melihat chat dari task yang dia buat
-   Auto-filter berdasarkan `created_by` field di tasks table

## 🚀 Cara Menggunakan

### Untuk User

1. Buka halaman task work wizard: `/user/task/{taskId}/work`
2. Klik tombol "Chat dengan Admin" (warna biru)
3. Ketik pesan di textarea
4. Tekan Enter atau klik "Kirim"
5. Pesan akan dikirim ke admin yang membuat task tersebut

### Untuk Admin

**Opsi 1: Via Task Chats Menu**

1. Login ke Filament panel
2. Klik menu "Task Chats" di sidebar
3. Lihat semua chat yang di-group by task
4. Klik "Reply" untuk membalas pesan user
5. Atau klik "Mark as Read" untuk tandai sudah dibaca

**Opsi 2: Via User Task Detail**

1. Buka menu "User Tasks"
2. Klik salah satu task untuk view detail
3. Scroll ke bawah, akan ada widget chat
4. Chat langsung dengan user di widget tersebut

## 📊 Database Queries

### Get unread messages for user

```php
$userTask->unreadMessagesForUser()->count();
```

### Get unread messages for admin

```php
$userTask->unreadMessagesForAdmin()->count();
```

### Get all messages for a task

```php
$userTask->messages()->with('user')->orderBy('created_at', 'asc')->get();
```

### Mark message as read

```php
$message->markAsRead();
```

## 🎯 Benefits

1. **Real-time Communication**

    - Admin dan user bisa diskusi langsung dalam website
    - Tidak perlu berpindah ke WhatsApp/platform lain

2. **Progress Tracking**

    - Admin bisa track komunikasi dengan setiap user
    - History tersimpan permanen di database

3. **Organized Management**

    - Chat di-group per task
    - Filter dan search untuk menemukan chat tertentu
    - Badge notifikasi untuk unread messages

4. **Better UX**

    - User tidak perlu keluar dari halaman task work
    - Admin bisa manage banyak chat dari satu dashboard
    - Auto-refresh untuk pesan baru

5. **Scalable**
    - Support multiple users per admin
    - Admin bisa handle banyak chat secara efisien
    - Database indexed untuk performance

## 🔧 Customization Options

### Polling Interval

Ubah di blade files:

```blade
<div wire:poll.10s="refreshMessages"></div>
<!-- Ganti 10s dengan interval yang diinginkan, misal: 5s, 15s, 30s -->
```

### Message Length Limit

Ubah di Livewire components:

```php
$this->validate([
    'newMessage' => 'required|string|max:1000', // Ganti 1000 dengan limit yang diinginkan
]);
```

### Chat Widget Height

Ubah di blade files:

```blade
style="max-height: 400px; min-height: 300px;"
<!-- Sesuaikan ukuran sesuai kebutuhan -->
```

## 📝 Future Enhancements

1. **Real-time dengan WebSocket**

    - Implementasi Laravel Echo + Pusher/Soketi
    - Push notifications untuk pesan baru

2. **File Upload**

    - Support attach gambar/file di chat
    - Preview media dalam chat bubble

3. **Typing Indicator**

    - Tampilkan "Admin is typing..." / "User is typing..."

4. **Emoji Support**

    - Emoji picker untuk pesan

5. **Search/Filter Chat**

    - Search dalam history chat
    - Filter by date range

6. **Export Chat History**

    - Download chat history sebagai PDF/Excel

7. **Chat Templates**
    - Quick replies untuk admin
    - Canned responses

## 🐛 Troubleshooting

### Chat tidak muncul

-   Pastikan `$userTask` tidak null
-   Check relasi `messages()` di model UserTask
-   Verify migration sudah dijalankan

### Pesan tidak auto-refresh

-   Check wire:poll directive ada di blade
-   Verify Livewire properly initialized
-   Check browser console untuk errors

### Badge notifikasi tidak update

-   Clear cache: `php artisan cache:clear`
-   Check query di `getNavigationBadge()`

### Permission issues

-   Verify user role (admin/superadmin)
-   Check `getEloquentQuery()` filter di resource

## ✅ Testing Checklist

-   [ ] User bisa send message di task work wizard
-   [ ] Admin bisa view message di Task Chats menu
-   [ ] Admin bisa reply dari table action
-   [ ] Admin bisa reply dari widget di detail page
-   [ ] Badge notifikasi update saat ada unread messages
-   [ ] Auto-refresh bekerja di kedua sisi (user & admin)
-   [ ] Mark as read functionality bekerja
-   [ ] Chat history tersimpan setelah refresh
-   [ ] Mobile responsive
-   [ ] Dark mode support
-   [ ] Admin filter hanya lihat task mereka sendiri
-   [ ] Superadmin bisa lihat semua chat

## 📞 Support

Jika ada pertanyaan atau issue, silakan hubungi developer atau buka issue di repository.
