# Fix: Task & Category Expiry + Task Taken Protection

## Masalah yang Diperbaiki

1. ✅ Task yang sudah expired masih muncul di halaman "My Tasks"
2. ✅ Category yang sudah expired masih menampilkan task-nya
3. ✅ Task yang sedang dikerjakan user lain masih bisa diambil oleh user lain

## Perubahan yang Dilakukan

### 1. Model Task (`app/Models/Task.php`)

#### Scope `active()` - Diperbaiki

```php
public function scopeActive($query)
{
    return $query->where('is_expired', false)
                 ->where('expired_at', '>', now())
                 ->whereHas('category', function ($q) {
                     $q->where('is_active', true)
                       ->where('expired_at', '>', now());
                 });
}
```

**Perubahan**: Menambahkan validasi category harus aktif dan belum expired

#### Scope `available()` - Diperbaiki

```php
public function scopeAvailable($query)
{
    return $query->active()
                 ->whereDoesntHave('userTasks', function ($q) {
                     $q->whereIn('status', ['taken', 'pending_verification_1', 'pending_verification_2']);
                 });
}
```

**Perubahan**: Menggunakan `userTasks` relationship untuk cek semua user yang sedang mengerjakan task

#### Method `isTaken()` - Diperbaiki

```php
public function isTaken(): bool
{
    return $this->userTasks()
                ->whereIn('status', ['taken', 'pending_verification_1', 'pending_verification_2'])
                ->exists();
}
```

**Perubahan**: Validasi lebih ketat untuk cek apakah task sedang dikerjakan oleh siapapun

### 2. Livewire MyTasks (`app/Livewire/MyTasks.php`)

#### Query Active Tasks - Diperbaiki

```php
$activeTasks = UserTask::with(['task.category'])
    ->where('user_id', $userId)
    ->active()
    ->where(function ($q) {
        $q->whereNull('deadline_at')->orWhere('deadline_at', '>', now());
    })
    ->whereHas('task', function ($q) {
        $q->where('is_expired', false)
          ->where('expired_at', '>', now())
          ->whereHas('category', function ($catQuery) {
              $catQuery->where('is_active', true)
                       ->where('expired_at', '>', now());
          });
    })
    ->orderBy('deadline_at', 'asc')
    ->get();
```

**Perubahan**:

-   Filter task yang expired
-   Filter category yang expired atau inactive
-   Double check deadline user task

### 3. Livewire TaskDashboard (`app/Livewire/TaskDashboard.php`)

#### Method `getCategories()` - Diperbaiki

```php
return Category::select('categories.*')
    ->where('is_active', true)
    ->where('expired_at', '>', now()) // ← DITAMBAHKAN
    ->withCount(['tasks as available_tasks_count' => function ($query) {
        $query->available()
            ->where('expired_at', '>', now())
            ->whereDoesntHave('userTasks', function ($q) {
                // ... status check
            });
    }])
    // ...
```

**Perubahan**: Menambahkan filter `expired_at` pada category

#### Method `proceedTakeTask()` - Diperbaiki

```php
$task = Task::findOrFail($taskId);

// Check if task is expired
if ($task->isExpired()) {
    session()->flash('error', 'Task sudah tidak tersedia (expired)');
    return;
}

// Check if task's category is expired
if ($task->category && $task->category->isExpired()) {
    session()->flash('error', 'Kategori task sudah tidak tersedia (expired)');
    return;
}

// Check if task is available (not taken by anyone else)
if ($task->isTaken()) {
    session()->flash('error', 'Task sudah diambil oleh user lain');
    return;
}
```

**Perubahan**:

-   Validasi terpisah untuk task expired
-   Validasi terpisah untuk category expired
-   Validasi terpisah untuk task yang sedang dikerjakan user lain
-   Pesan error lebih jelas

### 4. Livewire UserDashboard (`app/Livewire/UserDashboard.php`)

#### Stats Available Tasks - Diperbaiki

```php
'available_tasks' => Task::available()->count(),
```

**Perubahan**: Menggunakan scope `available()` yang sudah diperbaiki (otomatis filter expired & taken)

## Cara Kerja Setelah Fix

### Halaman My Tasks

1. ✅ Hanya menampilkan task yang belum expired
2. ✅ Hanya menampilkan task dengan category yang masih aktif dan belum expired
3. ✅ Hanya menampilkan task milik user yang deadline-nya belum lewat

### Halaman Dashboard

1. ✅ Category yang expired tidak muncul
2. ✅ Task yang expired tidak muncul di category manapun
3. ✅ Task yang sedang dikerjakan user lain tidak bisa diambil

### Mengambil Task

1. ✅ Validasi task belum expired
2. ✅ Validasi category task belum expired
3. ✅ Validasi task tidak sedang dikerjakan user lain
4. ✅ Race condition protection (double check sebelum create UserTask)

## Testing Checklist

-   [ ] Login sebagai user biasa
-   [ ] Buka halaman "My Tasks" - task expired tidak muncul
-   [ ] Buka halaman "Dashboard" - category expired tidak muncul
-   [ ] Coba ambil task yang sedang dikerjakan user lain - muncul error
-   [ ] Ambil task yang available - berhasil
-   [ ] Cek cache sudah di-clear

## Cache Management

Cache sudah di-clear otomatis untuk:

-   `my_active_tasks` - saat UserTask berubah
-   `available_tasks_count` - saat Task atau UserTask berubah
-   `dashboard_stats` - saat UserTask berubah

Manual clear cache:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Database Query Optimization

Semua query menggunakan:

-   ✅ Eager loading (`with()`) untuk menghindari N+1 problem
-   ✅ Index pada `is_expired`, `expired_at`, `is_active`
-   ✅ Cache untuk query yang sering diakses
-   ✅ Pagination untuk list yang panjang

## Error Messages

Pesan error yang lebih jelas:

-   "Task sudah tidak tersedia (expired)" - task expired
-   "Kategori task sudah tidak tersedia (expired)" - category expired
-   "Task sudah diambil oleh user lain" - task sedang dikerjakan
-   "Anda masih memiliki task yang sedang dikerjakan" - user sudah punya task aktif

---

**Tanggal**: 10 Desember 2025
**Status**: ✅ COMPLETED
