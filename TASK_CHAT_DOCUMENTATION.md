# Task Chat System - Documentation

## Overview

Sistem chat yang terintegrasi penuh dengan Filament Admin Panel untuk komunikasi antara Admin dan User berdasarkan task mereka.

## Features

### ✨ Fitur Utama

1. **Real-time Chat**

    - Auto-refresh setiap 5-10 detik menggunakan Livewire polling
    - Scroll otomatis ke pesan terbaru
    - Notifikasi untuk pesan baru

2. **File Sharing**

    - Upload file (images, PDF, documents)
    - Preview untuk gambar
    - Download untuk file lainnya
    - Max file size: 10MB
    - Supported formats: jpg, jpeg, png, pdf, doc, docx, xls, xlsx, zip

3. **Read Status**

    - Mark messages as read/unread
    - Visual indicators untuk unread messages
    - Timestamp untuk setiap pesan

4. **Quick Actions**
    - Quick Reply: Kirim pesan cepat tanpa membuka modal chat
    - Mark as Read: Tandai pesan sebagai sudah dibaca
    - Bulk Actions: Mark multiple chats as read

## Components

### 1. TaskChatsRelationManager

**Location:** `app/Filament/Resources/Categories/RelationManagers/TaskChatsRelationManager.php`

**Features:**

-   Tampilkan semua chat untuk category tertentu
-   Badge untuk unread messages
-   Modal chat dengan slide-over
-   Quick reply functionality
-   Bulk mark as read

**Actions:**

-   `chat`: Buka modal chat lengkap
-   `mark_all_read`: Tandai semua pesan sebagai sudah dibaca
-   `quick_reply`: Kirim pesan cepat

### 2. TaskChatsOverview Page

**Location:** `app/Filament/Pages/TaskChatsOverview.php`

**Features:**

-   Dashboard overview semua chats
-   Statistics cards:
    -   Total Chats
    -   Unread Messages
    -   Active Today
    -   Total Messages
-   Filters:
    -   By status
    -   Unread only
    -   Active today
-   Auto-refresh setiap 10 detik

**Access:** Navigation menu → Communication → Task Chats

### 3. TaskChat Livewire Component

**Location:** `app/Livewire/TaskChat.php`

**Features:**

-   Real-time message loading
-   File upload dengan preview
-   Auto-scroll ke pesan terbaru
-   Mark messages as read otomatis
-   Notifications untuk admin

**Public Methods:**

-   `sendMessage()`: Kirim pesan baru
-   `refreshMessages()`: Refresh daftar pesan
-   `removeFile()`: Hapus file yang akan diupload
-   `markMessagesAsRead()`: Tandai pesan sebagai sudah dibaca

## Usage

### Untuk Admin

#### 1. Melihat Semua Chats

```
1. Buka Filament Admin Panel
2. Klik "Task Chats" di menu Navigation
3. Lihat dashboard dengan statistics dan list semua chats
```

#### 2. Membuka Chat dengan User

```
1. Dari TaskChatsOverview atau RelationManager
2. Klik tombol "Open Chat" pada row yang diinginkan
3. Modal slide-over akan terbuka dengan chat history
4. Ketik pesan atau upload file
5. Klik Send atau tekan Enter
```

#### 3. Quick Reply

```
1. Klik tombol "Quick Reply" pada row
2. Ketik pesan di form yang muncul
3. Upload file jika diperlukan (optional)
4. Klik Submit
5. Pesan akan terkirim tanpa membuka modal chat
```

#### 4. Mark Messages as Read

```
- Single: Klik "Mark as Read" pada row tertentu
- Bulk: Select multiple rows → "Mark All as Read"
- Auto: Pesan akan otomatis marked as read saat membuka chat
```

### Untuk User

#### 1. Membuka Chat

```
1. Login ke aplikasi
2. Buka task yang sedang dikerjakan
3. Klik tab/tombol "Chat"
4. Chat interface akan muncul
```

#### 2. Mengirim Pesan

```
1. Ketik pesan di textarea
2. Untuk new line: Shift+Enter
3. Untuk kirim: Enter atau klik tombol Send
```

#### 3. Upload File

```
1. Klik icon attachment
2. Pilih file (max 10MB)
3. File preview akan muncul
4. Klik Send untuk kirim
5. Untuk cancel: Klik icon X pada preview
```

## API Reference

### TaskMessage Model

```php
// Sender types
TaskMessage::SENDER_USER = 'user'
TaskMessage::SENDER_ADMIN = 'admin'

// Create new message
TaskMessage::create([
    'user_task_id' => $userTaskId,
    'user_id' => auth()->id(),
    'sender_type' => TaskMessage::SENDER_ADMIN,
    'message' => 'Hello!',
    'file_path' => $filePath, // optional
    'file_name' => $fileName, // optional
    'file_type' => $fileType, // optional
    'file_size' => $fileSize, // optional
    'is_read' => false,
]);

// Mark as read
$userTask->messages()
    ->where('sender_type', 'user')
    ->where('is_read', false)
    ->update([
        'is_read' => true,
        'read_at' => now(),
    ]);

// Get unread count
$unreadCount = $userTask->messages()
    ->where('sender_type', 'user')
    ->where('is_read', false)
    ->count();
```

### Livewire Events

```php
// Refresh messages
$this->dispatch('refresh-messages');

// Scroll to bottom
$this->dispatch('scroll-to-bottom');

// Message sent
$this->dispatch('message-sent', taskId: $taskId);
```

## Customization

### Change Polling Interval

```php
// In TaskChatsOverview.php or TaskChatsRelationManager.php
->poll('10s') // Change to desired interval: 5s, 30s, 1m, etc.
```

### Change File Upload Settings

```php
// In TaskChat.php validation rules
'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip',

// Max size: 10240 KB = 10 MB
// Add more mime types as needed
```

### Custom Notifications

```php
Notification::make()
    ->title('Custom Title')
    ->body('Custom message')
    ->success() // or ->danger(), ->warning(), ->info()
    ->send();
```

## Performance Tips

1. **Polling Interval**

    - Default: 10 seconds untuk overview page
    - Default: 5 seconds untuk chat component
    - Adjust based on your server capacity

2. **Message Pagination**

    - Currently loads all messages
    - For heavy usage, implement pagination:

    ```php
    $this->messages = $this->userTask->messages()
        ->latest()
        ->limit(50)
        ->get()
        ->reverse()
        ->values()
        ->toArray();
    ```

3. **File Storage**
    - Files stored in `storage/app/public/task-messages`
    - Consider implementing file cleanup for old messages
    - Use CDN for better performance

## Troubleshooting

### Messages not updating

1. Check Livewire is working: `php artisan livewire:discover`
2. Clear cache: `php artisan cache:clear`
3. Check polling is enabled in table

### File upload not working

1. Check storage link: `php artisan storage:link`
2. Check file permissions: `chmod -R 755 storage`
3. Check upload_max_filesize in php.ini

### Notifications not showing

1. Import Notification class: `use Filament\Notifications\Notification;`
2. Check user is authenticated
3. Clear browser cache

## Security Considerations

1. **File Upload Validation**

    - Always validate file types and sizes
    - Scan uploaded files for malware
    - Store files outside public directory

2. **Message Sanitization**

    - Messages are escaped with `e()` helper
    - Use `\App\Helpers\ChatHelper::linkify()` for safe URL conversion
    - Never use raw HTML from user input

3. **Access Control**
    - Users can only access their own task chats
    - Admins can access all chats
    - Implement proper authorization checks

## Future Enhancements

-   [ ] WebSocket integration for true real-time chat
-   [ ] Typing indicators
-   [ ] Message editing and deletion
-   [ ] Emoji support
-   [ ] Voice messages
-   [ ] Read receipts
-   [ ] Push notifications
-   [ ] Chat search functionality
-   [ ] Export chat history
-   [ ] Chat templates for quick responses

## Support

For issues or questions, contact your system administrator.

---

**Last Updated:** December 8, 2025
**Version:** 1.0.0
