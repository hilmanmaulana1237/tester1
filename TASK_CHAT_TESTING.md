# 🧪 Task Chat System - Testing Guide

## Quick Testing Steps

### 1️⃣ First Time Setup (Only Once)

```bash
# Pastikan storage link sudah dibuat
php artisan storage:link

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Discover Livewire components
php artisan livewire:discover
```

### 2️⃣ Test Admin Panel

#### A. Access TaskChatsOverview Page

1. Login sebagai Admin
2. Lihat menu navigasi
3. **Harusnya ada menu baru: "Task Chats"** di bagian Communication
4. Klik menu tersebut
5. ✅ **Expected:** Dashboard dengan 4 statistics cards muncul
6. ✅ **Expected:** Table dengan list chats muncul

#### B. Test Statistics Cards

Pastikan cards menampilkan:

-   Total Chats (jumlah UserTasks yang punya messages)
-   Unread Messages (jumlah unread dari user)
-   Active Today (chats aktif hari ini)
-   Total Messages (total semua pesan)

#### C. Test Table Actions

**Test 1: Open Chat**

1. Klik button "Open Chat" pada row manapun
2. ✅ Modal slide-over harusnya muncul dari kanan
3. ✅ Chat interface dengan header gradient muncul
4. ✅ Messages history tampil
5. Coba ketik dan kirim pesan
6. ✅ Pesan terkirim dan muncul di chat
7. Close modal
8. ✅ Badge unread harusnya berkurang/hilang

**Test 2: Quick Reply**

1. Klik button "Quick Reply" pada row manapun
2. ✅ Form modal muncul
3. Ketik pesan test
4. (Optional) Upload file
5. Submit
6. ✅ Notification "Message sent" muncul
7. ✅ Pesan terkirim ke user

**Test 3: Mark as Read**

1. Cari row yang ada badge unread
2. Klik "Mark as Read"
3. ✅ Badge unread hilang
4. ✅ Notification muncul

**Test 4: Bulk Actions**

1. Select beberapa rows (checkbox)
2. Klik dropdown bulk actions
3. Pilih "Mark All as Read"
4. ✅ Semua unread di rows yang dipilih jadi read
5. ✅ Notification dengan count muncul

#### D. Test Auto-Refresh

1. Buka TaskChatsOverview page
2. **Tunggu 10 detik**
3. ✅ Table harusnya auto-refresh (lihat loading indicator)
4. Buka chat modal
5. **Tunggu 5 detik**
6. ✅ Messages harusnya auto-refresh

#### E. Test Filters

1. Click filter icon
2. Test filter "Unread Messages Only"
    - ✅ Hanya show chats dengan unread
3. Test filter "Active Today"
    - ✅ Hanya show chats aktif hari ini
4. Test filter by Status
    - ✅ Filter by taken, completed, etc works

### 3️⃣ Test From Category Resource

#### Access via RelationManager

1. Go to Categories resource
2. Select any category
3. Look for tab **"Task Chats"**
4. ✅ Same table & features as overview
5. Test all actions (sama seperti di atas)

### 4️⃣ Test User Side

#### User Chat Interface

1. Login sebagai User
2. Go to My Tasks
3. Select task yang assigned ke user
4. Look for Chat tab/button
5. ✅ Chat interface loads
6. Test send message:
    - Type message
    - Press Enter atau click Send
    - ✅ Message appears in chat
7. Test file upload:
    - Click attach icon
    - Select file (< 10MB)
    - ✅ Preview muncul
    - Send
    - ✅ File terkirim

### 5️⃣ Test File Sharing

#### Image Upload

1. Upload image (.jpg, .png)
2. ✅ Preview image muncul di chat
3. Click image
4. ✅ Image buka di tab baru

#### Document Upload

1. Upload PDF/DOC
2. ✅ File card dengan icon muncul
3. Click download icon
4. ✅ File terdownload

### 6️⃣ Test Notifications

#### Admin Notifications

1. Login sebagai Admin
2. Open chat atau quick reply
3. Send message
4. ✅ Success notification muncul
5. Mark as read
6. ✅ Notification dengan count muncul

### 7️⃣ Test Real-time Features

#### Polling Test

1. Open 2 browser windows:
    - Window 1: Admin (chat open)
    - Window 2: User (same task chat)
2. Send message dari User
3. **Wait 5 seconds**
4. ✅ Message appear di Admin window (auto-refresh)

#### Badge Update Test

1. Admin: Open TaskChatsOverview
2. User: Send message
3. **Wait 10 seconds**
4. ✅ Badge counter harusnya update
5. ✅ Unread count di stats update

### 8️⃣ Test Edge Cases

#### Empty State

1. Find/create task tanpa messages
2. Open chat
3. ✅ "Belum ada percakapan" message muncul

#### Send Empty Message

1. Try send message kosong (no text, no file)
2. ✅ Error message "Pesan atau file harus diisi"

#### Large File

1. Try upload file > 10MB
2. ✅ Error message file too large

#### Invalid File Type

1. Try upload .exe atau file type lain
2. ✅ Error invalid file type

### 9️⃣ Test Read Status

#### Read Receipts

1. User sends message
2. Check message has no checkmark
3. Admin opens chat
4. ✅ Message auto-marked as read
5. User sees message
6. ✅ Checkmark (✓✓) appears

### 🔟 Test Search & Filters

#### Search

1. Go to TaskChatsOverview
2. Use search box
3. Search by:
    - User name ✅
    - Email ✅
    - Task title ✅
4. ✅ Results filter correctly

---

## 🐛 Troubleshooting

### Issue: Menu "Task Chats" tidak muncul

**Solution:**

```bash
php artisan cache:clear
php artisan config:clear
```

### Issue: Modal tidak muncul

**Solution:**

1. Check browser console for JS errors
2. Clear browser cache
3. Hard refresh (Ctrl+F5)

### Issue: Messages tidak auto-refresh

**Solution:**

1. Check Livewire is working
2. Check polling attribute in code
3. Clear all caches

### Issue: File upload gagal

**Solution:**

```bash
# Check storage link
php artisan storage:link

# Check permissions (Linux/Mac)
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Issue: Notifications tidak muncul

**Solution:**

1. Make sure Filament Notifications are imported correctly
2. Check browser console
3. Clear cache

---

## ✅ Expected Results Summary

| Feature                      | Expected Result                   |
| ---------------------------- | --------------------------------- |
| TaskChatsOverview accessible | ✅ Menu muncul, page loads        |
| Statistics accurate          | ✅ Numbers correct                |
| Table auto-refresh           | ✅ Every 10 seconds               |
| Open Chat works              | ✅ Slide-over modal               |
| Quick Reply works            | ✅ Form modal, message sent       |
| Mark as Read works           | ✅ Badge updates                  |
| Bulk actions work            | ✅ Multiple updates               |
| Filters work                 | ✅ Correct filtering              |
| Search works                 | ✅ Finds results                  |
| User chat works              | ✅ Send/receive messages          |
| File upload works            | ✅ Images preview, files download |
| Auto-refresh works           | ✅ Messages update automatically  |
| Notifications work           | ✅ Toast notifications appear     |
| Read status works            | ✅ Checkmarks appear              |

---

## 📊 Performance Benchmarks

### Expected Load Times:

-   TaskChatsOverview page: < 2 seconds
-   Open Chat modal: < 1 second
-   Send message: < 500ms
-   Auto-refresh: < 300ms
-   File upload (5MB): < 5 seconds

### Database Queries:

-   Overview page: ~5-8 queries (with eager loading)
-   Open chat: ~3-4 queries
-   Send message: 1-2 queries

---

## 🎯 Checklist

Print this and check off as you test:

```
□ Setup completed (storage link, cache clear)
□ TaskChatsOverview page accessible
□ Statistics cards display correctly
□ Table loads with data
□ Table auto-refreshes (wait 10s)
□ Open Chat modal works
□ Chat messages display
□ Send message works
□ File upload works
□ File preview/download works
□ Quick Reply works
□ Mark as Read works
□ Bulk Mark as Read works
□ Filters work (status, unread, active)
□ Search works
□ User chat interface works
□ Auto-refresh in chat (wait 5s)
□ Notifications appear
□ Read receipts work
□ Badge counters update
□ RelationManager tab works
□ Edge cases handled (empty, errors)
□ Mobile responsive (if applicable)
□ No console errors
□ No PHP errors in logs
```

---

## 🚀 Ready for Production?

Before going live, ensure:

-   [ ] All tests passed
-   [ ] No errors in logs
-   [ ] Performance acceptable
-   [ ] User training completed
-   [ ] Documentation reviewed
-   [ ] Backup strategy in place

---

## 📝 Test Results Template

```
Date: _______________
Tester: _______________
Environment: _______________

PASS/FAIL SUMMARY:
- Setup: _______________
- Admin Features: _______________
- User Features: _______________
- Real-time Updates: _______________
- File Sharing: _______________
- Notifications: _______________
- Performance: _______________

ISSUES FOUND:
1. _______________
2. _______________
3. _______________

NOTES:
_______________________________________________
_______________________________________________
_______________________________________________

OVERALL STATUS: ☐ PASS  ☐ FAIL
```

---

**Happy Testing! 🧪✨**
