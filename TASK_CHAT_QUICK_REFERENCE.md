# 🚀 Task Chat System - Quick Reference

## 🎯 Quick Start (5 Minutes)

### 1. Setup (First Time Only)

```bash
# Create storage link
php artisan storage:link

# Clear all caches
php artisan optimize:clear

# Or individually:
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 2. Access Admin Panel

```
URL: /admin/task-chats
Menu: Communication → Task Chats
```

### 3. Test Basic Functions

-   Click "Open Chat" → Should open modal
-   Type message → Should send
-   Upload file → Should upload
-   Wait 10s → Table should refresh

---

## 📁 File Locations

### Backend (PHP)

```
app/Filament/Pages/
└── TaskChatsOverview.php                    ← Main dashboard page

app/Filament/Resources/Categories/RelationManagers/
└── TaskChatsRelationManager.php             ← Category chat view

app/Livewire/
└── TaskChat.php                             ← Chat component

app/Models/
├── TaskMessage.php                          ← Already exists
└── UserTask.php                             ← Already exists
```

### Frontend (Blade)

```
resources/views/filament/pages/
└── task-chats-overview.blade.php            ← Dashboard view

resources/views/livewire/
└── task-chat.blade.php                      ← Chat UI
```

### Documentation

```
TASK_CHAT_DOCUMENTATION.md                   ← Full docs
TASK_CHAT_IMPROVEMENTS.md                    ← Quick guide
TASK_CHAT_COMPLETE_SUMMARY.md                ← Overview
TASK_CHAT_TESTING.md                         ← Testing guide
TASK_CHAT_QUICK_REFERENCE.md                 ← This file
```

---

## ⚙️ Configuration

### Polling Intervals (Default)

```php
// Change in respective files:

// TaskChatsOverview.php (line ~40)
->poll('10s')  // Table refresh

// task-chat.blade.php (line ~1)
wire:poll.5s   // Chat messages refresh
```

### File Upload Limits

```php
// TaskChat.php validation (line ~44)
'file' => 'nullable|file|max:10240|mimes:...'
// max:10240 = 10MB

// Change to 20MB:
'file' => 'nullable|file|max:20480|mimes:...'
```

### Accepted File Types

```php
// Current: jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip

// Add more:
mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip,rar,txt,csv
```

---

## 🔑 Key Features

### Admin Actions

| Action       | Description     | Location     |
| ------------ | --------------- | ------------ |
| Open Chat    | Full chat modal | All tables   |
| Quick Reply  | Fast response   | All tables   |
| Mark as Read | Single mark     | All tables   |
| Bulk Mark    | Multiple mark   | Bulk actions |

### Auto Features

| Feature          | Interval     | Configurable |
| ---------------- | ------------ | ------------ |
| Table Refresh    | 10s          | Yes          |
| Chat Refresh     | 5s           | Yes          |
| Mark as Read     | Auto on open | No           |
| Scroll to Bottom | Auto         | No           |

---

## 🎨 UI Components

### Statistics Cards (4)

```
Total Chats    | Unread Messages | Active Today | Total Messages
```

### Table Columns

```
Avatar | User Name | Task | Status | Messages | Unread | Last Message | Updated
```

### Filters

```
- Status (dropdown)
- Unread Only (toggle)
- Active Today (toggle)
```

---

## 🔧 Common Commands

### Development

```bash
# Watch for changes
npm run dev

# Build for production
npm run build

# Clear Livewire cache
php artisan livewire:discover

# View routes
php artisan route:list | grep chat

# Check logs
tail -f storage/logs/laravel.log
```

### Maintenance

```bash
# Clear old messages (example - create this)
php artisan task-chat:cleanup --days=30

# Backup database
php artisan backup:run

# Check storage usage
du -sh storage/app/public/task-messages
```

---

## 🐛 Quick Fixes

### Problem: Menu not showing

```bash
php artisan optimize:clear
# Refresh browser
```

### Problem: Modal not opening

```bash
# Check browser console
# Clear browser cache: Ctrl+Shift+Delete
# Hard refresh: Ctrl+F5
```

### Problem: File upload fails

```bash
php artisan storage:link
chmod -R 755 storage  # Linux/Mac only
```

### Problem: Messages not refreshing

```bash
# Check Livewire
php artisan livewire:discover

# Check polling in code
# Look for: wire:poll or ->poll()
```

---

## 📊 Database Queries

### Get Unread Count

```php
$unreadCount = TaskMessage::where('sender_type', 'user')
    ->where('is_read', false)
    ->count();
```

### Get Active Today Count

```php
$activeToday = UserTask::whereHas('messages', function($q) {
    $q->whereDate('created_at', today());
})->count();
```

### Mark All as Read for User Task

```php
$userTask->messages()
    ->where('sender_type', 'user')
    ->where('is_read', false)
    ->update([
        'is_read' => true,
        'read_at' => now(),
    ]);
```

---

## 🎯 URLs

### Admin Panel

```
/admin                          → Dashboard
/admin/task-chats              → Chat Overview (NEW)
/admin/categories              → Categories
/admin/categories/{id}         → Category Detail → Task Chats tab
```

### User Panel

```
/tasks                         → My Tasks
/tasks/{id}                    → Task Detail → Chat tab
```

---

## 🔐 Permissions

### Admin Can:

✅ View all chats  
✅ Send messages to any user  
✅ Mark messages as read  
✅ Upload files  
✅ Access dashboard  
✅ Use quick reply  
✅ Bulk actions

### User Can:

✅ View own task chats  
✅ Send messages to admin  
✅ Upload files  
✅ See read status  
❌ Access admin dashboard  
❌ View other users' chats

---

## 📈 Performance Tips

### Optimization

```php
// Add indexes (if not exists)
Schema::table('task_messages', function (Blueprint $table) {
    $table->index(['user_task_id', 'is_read']);
    $table->index(['sender_type', 'is_read']);
    $table->index('created_at');
});

// Use eager loading
UserTask::with(['user', 'task', 'messages'])
    ->whereHas('messages')
    ->get();
```

### Caching (Optional)

```php
// Cache statistics
$stats = Cache::remember('task-chat-stats', 60, function() {
    return [
        'total' => UserTask::whereHas('messages')->count(),
        'unread' => TaskMessage::where('sender_type', 'user')
            ->where('is_read', false)->count(),
        // ... etc
    ];
});
```

---

## 🎓 Tips & Tricks

### For Admins

1. Use keyboard shortcuts: Enter to send, Shift+Enter for new line
2. Filter by "Unread Only" for priority
3. Use Quick Reply for multiple chats
4. Monitor badge counters for new messages

### For Developers

1. Adjust polling based on server load
2. Implement pagination for heavy usage
3. Add indexes for better performance
4. Monitor error logs regularly

### For Users

1. Upload files directly in chat
2. Press Enter to send quickly
3. Scroll up to see history
4. Check timestamps for response time

---

## 📞 Support

### Check First:

1. Documentation: `TASK_CHAT_DOCUMENTATION.md`
2. Testing guide: `TASK_CHAT_TESTING.md`
3. Error logs: `storage/logs/laravel.log`

### Common Issues:

-   Menu not showing → Clear cache
-   Modal not opening → Clear browser cache
-   File upload fails → Check storage link
-   No auto-refresh → Check polling settings

---

## 🎉 Feature Highlights

```
┌──────────────────────────────────────┐
│  ✨ HIGHLIGHTS                       │
├──────────────────────────────────────┤
│  🎯 Real-time Updates (5-10s)        │
│  💬 Full Chat Interface              │
│  ⚡ Quick Reply                       │
│  📊 Live Statistics                  │
│  📎 File Sharing                     │
│  ✅ Read Receipts                    │
│  🔔 Notifications                    │
│  🎨 Modern UI/UX                     │
│  📱 Responsive Design                │
│  🔒 Secure & Validated               │
└──────────────────────────────────────┘
```

---

## 🚦 Status Check

Quick health check:

```bash
# Check if files exist
ls -la app/Filament/Pages/TaskChatsOverview.php
ls -la resources/views/filament/pages/task-chats-overview.blade.php

# Check storage permissions
ls -la storage/app/public/task-messages

# Check logs for errors
tail -n 50 storage/logs/laravel.log | grep ERROR
```

---

## 📚 Related Documentation

-   Laravel: https://laravel.com/docs
-   Filament: https://filamentphp.com/docs
-   Livewire: https://livewire.laravel.com/docs

---

## ✅ Quick Checklist

Before you start using:

-   [ ] Storage link created
-   [ ] Caches cleared
-   [ ] Can access admin panel
-   [ ] Can see Task Chats menu
-   [ ] Can open chat modal
-   [ ] Can send message
-   [ ] Can upload file
-   [ ] Auto-refresh works

---

**Version:** 1.0.0  
**Last Updated:** December 8, 2025  
**Status:** ✅ Production Ready

---

**Need Help?** Check full documentation or contact system administrator.
