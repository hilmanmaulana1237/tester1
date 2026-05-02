# 🧪 TESTING CHAT - PANDUAN LENGKAP

## Quick Test (5 Menit)

### 1. Start Services

```bash
# Option A: Auto-start (Recommended)
.\start-chat.bat

# Option B: Manual (3 terminals)
# Terminal 1:
php artisan reverb:start

# Terminal 2:
php artisan serve

# Terminal 3:
npm run dev
```

### 2. Test UI/UX ✅

**Login ke Admin:**

```
http://localhost:8000/admin
```

**Buka User Task → Klik Chat:**

1. ✅ **Header Hijau** - Gradient emerald-600 to green-600
2. ✅ **Avatar Initial** - Circle dengan huruf pertama nama
3. ✅ **Online Badge** - Hijau animated pulse
4. ✅ **Task Title** - Truncated di 40 karakter

### 3. Test Send Message ✅

**Ketik di textarea:**

```
Halo, ini test message
```

**Tekan Enter atau klik 🚀:**

-   ✅ Message terkirim
-   ✅ Modal **TIDAK close**
-   ✅ Bubble hijau muncul di kanan
-   ✅ Auto-scroll ke bawah
-   ✅ Timestamp muncul
-   ✅ Read status (✓✓) muncul

**Expected Result:**

```
Modal tetap terbuka ✅
Message hijau di kanan ✅
Textarea kosong lagi ✅
```

### 4. Test Upload File 📎

**Klik icon 📎:**

1. Select image (JPG/PNG) atau PDF
2. ✅ Preview muncul dengan emoji 📄
3. ✅ Nama file + size tampil
4. **Optional:** Tulis message juga
5. Klik 🚀 send

**Expected Result:**

```
File ter-upload ✅
Preview image muncul di bubble ✅
Link download untuk PDF ✅
Modal tetap terbuka ✅
```

### 5. Test Cache ⚡

**Test 1 - Load Speed:**

```
1. Buka chat pertama kali (slow - hit DB)
2. Close modal
3. Buka chat lagi (fast - hit cache) ✅
```

**Verify di Browser DevTools:**

```javascript
// Console → Network tab
// Request ke /livewire/update should be fast (<50ms)
```

**Test 2 - Cache Invalidation:**

```
1. Send message
2. Cache auto-clear ✅
3. New message langsung muncul ✅
```

### 6. Test Real-Time Broadcasting 🔴

**Setup 2 Browser:**

**Browser 1 (Admin):**

```
Login: admin@example.com
Open chat for Task #1
```

**Browser 2 (User):**

```
Login: user yang punya Task #1
Open same chat
```

**Test Steps:**

1. **Admin send:** "Test from admin"
2. ✅ **User melihat instant** tanpa refresh
3. ✅ **Browser notification muncul** di User
4. **User reply:** "Test from user"
5. ✅ **Admin melihat instant** tanpa refresh
6. ✅ **Browser notification muncul** di Admin

**Expected Console Log:**

```javascript
// Browser 2 Console (User)
Echo connected to chat.1
MessageSent event received
{ message: "Test from admin", user: {...} }
```

### 7. Test Form Behavior ✅

**Test Enter Key:**

```
Type: "Line 1"
Press: Enter
Result: Message sent ✅
```

**Test Shift+Enter:**

```
Type: "Line 1"
Press: Shift+Enter
Type: "Line 2"
Press: Enter
Result: Multi-line message sent ✅
```

**Test Click Outside:**

```
Click textarea → Type message
Click empty area in modal
Result: Modal tidak close, focus tetap ✅
```

**Test Send Button:**

```
Click 🚀 button
Result: Message sent, modal TIDAK close ✅
```

---

## 🎨 Visual Checklist

### Header

-   [x] Background: Gradient hijau (emerald → green)
-   [x] Avatar: White background, emoji 💬
-   [x] Green dot pulse animation
-   [x] "Online" badge hijau
-   [x] Shadow bawah header

### Messages

-   [x] My messages: Hijau di kanan
-   [x] Other messages: Abu-abu di kiri
-   [x] Avatar circle dengan initial
-   [x] Nama user di atas bubble
-   [x] Timestamp di bawah bubble
-   [x] Read status ✓✓ untuk my messages
-   [x] Rounded corners smooth
-   [x] Proper spacing antar message

### Input Area

-   [x] Fixed di bawah
-   [x] Border top abu-abu tipis
-   [x] File preview dengan emoji
-   [x] Error message merah
-   [x] Textarea auto-resize
-   [x] Upload button 📎
-   [x] Send button 🚀 hijau gradient
-   [x] Hint text kecil

### Empty State

-   [x] Icon 💬 besar di tengah
-   [x] Text "Belum Ada Pesan"
-   [x] Subtitle "Mulai percakapan..."

---

## 🔧 Debug Commands

### Check Cache

```php
php artisan tinker

// Check if cache exists
cache()->has("chat_messages_1"); // true = cached

// View cached data
cache()->get("chat_messages_1");

// Clear specific cache
cache()->forget("chat_messages_1");

// Clear all cache
cache()->flush();
```

### Check Broadcasting

```bash
# Check Reverb status
php artisan reverb:start --debug

# Check connections
# Output should show WebSocket connections when chat opened
```

### Browser Console Tests

```javascript
// Test Echo connection
Echo.connector.pusher.connection.state;
// Should return: "connected"

// Test channel subscription
Echo.connector.pusher.channels.channels;
// Should show: "private-chat.1" etc

// Manual refresh test
Livewire.find("component-id").call("refreshMessages");

// Test notification permission
Notification.permission; // "granted", "denied", or "default"
```

### Network Tab

```
Filter: /livewire/update
Check: Response time < 100ms (cached)
Check: Payload size reasonable
```

---

## ⚠️ Common Issues & Fixes

### Issue 1: Send Button Close Modal

**Symptom:** Klik 🚀 → modal close
**Fix:**

```bash
# Rebuild assets
npm run build

# Clear cache
php artisan filament:cache-components

# Hard refresh browser (Ctrl+Shift+R)
```

### Issue 2: Theme Masih Ungu

**Check:**

```css
/* theme.css should have: */
--primary-500: 16 185 129; /* emerald-600 */
--primary-600: 5 150 105; /* green-600 */
```

**Fix:**

```bash
npm run build
# Clear browser cache
```

### Issue 3: Messages Tidak Real-Time

**Check Reverb:**

```bash
php artisan reverb:start --debug
# Should show connections
```

**Check .env:**

```
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
```

**Check Browser Console:**

```javascript
Echo.connector.pusher.connection.state;
// If "disconnected" → Reverb not running
```

### Issue 4: Cache Tidak Bekerja

**Check config/cache.php:**

```php
'default' => env('CACHE_STORE', 'file'),
```

**Test:**

```bash
php artisan tinker
cache()->put('test', 'value', 60);
cache()->get('test'); // Should return "value"
```

---

## 📊 Performance Benchmarks

### Expected Results

**Page Load (First Time):**

-   DOM Ready: < 500ms
-   Assets Loaded: < 1s
-   Chat Rendered: < 800ms

**Message Send:**

-   Click to Send: < 100ms
-   DB Insert: < 50ms
-   UI Update: < 100ms
-   Total: < 250ms ✅

**Message Receive (Real-Time):**

-   Event Broadcast: < 50ms
-   Echo Receive: < 100ms
-   UI Update: < 100ms
-   Total: < 250ms ✅

**Cache Performance:**

-   First Load (No Cache): ~150ms
-   Cached Load: ~10ms ⚡
-   Speed Up: 15x faster!

---

## ✅ Success Criteria

### UI/UX

-   [x] Tema hijau emerald (not ungu)
-   [x] Layout rapi dengan spacing konsisten
-   [x] Responsive di berbagai ukuran layar
-   [x] Dark mode support

### Functionality

-   [x] Send message tanpa close modal
-   [x] File upload dengan preview
-   [x] Multi-line message (Shift+Enter)
-   [x] Read status tracking
-   [x] Auto-scroll ke pesan baru

### Performance

-   [x] Message cache (30s TTL)
-   [x] Lazy load dengan eager loading
-   [x] Real-time < 250ms latency
-   [x] No N+1 queries

### User Experience

-   [x] Browser notifications
-   [x] Typing shortcuts (Enter/Shift+Enter)
-   [x] Empty state guidance
-   [x] Error handling dengan pesan jelas
-   [x] Loading states

---

## 🎉 Final Check

Sebelum declare DONE, pastikan semua ini ✅:

```bash
# 1. Services running
pgrep -f "artisan reverb"  # Should return PID
pgrep -f "artisan serve"   # Should return PID
pgrep -f "vite"            # Should return PID

# 2. Assets built
ls public/build/assets/theme-*.css  # Should exist

# 3. Cache working
php artisan tinker
cache()->has("chat_messages_1");  # true after first load

# 4. Broadcasting connected
# Browser console:
Echo.connector.pusher.connection.state == "connected"  # true
```

**Kalau semua ✅ → DONE! Chat production-ready! 🚀**
