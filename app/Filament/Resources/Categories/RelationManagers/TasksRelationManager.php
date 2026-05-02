<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use App\Models\Task;
use App\Models\UserTask;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';
    protected static ?string $recordTitleAttribute = 'title';

    // ⬇️ v4: Schema, bukan Form - Synced with TaskForm.php
    public function form(Schema $schema): Schema
    {
        $isLocked = fn(?Task $record) => $record?->isTaken() === true;

        return $schema->schema([
            Forms\Components\TextInput::make('title')
                ->label('Title')
                ->required()
                ->maxLength(255)
                ->disabled($isLocked)
                ->columnSpanFull(),

            Forms\Components\FileUpload::make('vcf_data')
                ->label('File VCF (Kontak)')
                ->acceptedFileTypes(['text/vcard', 'text/x-vcard', '.vcf'])
                ->disk('public')
                ->directory('vcf-files')
                ->visibility('public')
                ->maxSize(5120)
                ->helperText('Upload file .vcf berisi data kontak. User bisa download file ini dari halaman tugas.')
                ->disabled($isLocked)
                ->columnSpanFull(),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->required()
                ->rows(3)
                ->disabled($isLocked)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('whatsapp_group_link')
                ->label('Whatsapp group link')
                ->required()
                ->url()
                ->maxLength(2048)
                ->disabled($isLocked),

            Forms\Components\TextInput::make('tutorial_link')
                ->label('Link Panduan/Tutorial')
                ->placeholder('https://contoh.com/panduan')
                ->url()
                ->helperText('Tautan panduan yang akan ditampilkan di wizard pengguna.')
                ->disabled($isLocked),

            Forms\Components\Select::make('difficulty_level')
                ->label('Difficulty level')
                ->options(Task::DIFFICULTIES)
                ->required()
                ->default('easy')
                ->disabled($isLocked),

            Forms\Components\TextInput::make('estimated_amount')
                ->label('Estimasi Nominal (Rp)')
                ->numeric()
                ->prefix('Rp')
                ->placeholder('Contoh: 50000')
                ->helperText('Perkiraan bayaran untuk task ini. User akan melihat nominal ini.')
                ->disabled($isLocked)
                ->columnSpanFull(),

            Forms\Components\DateTimePicker::make('expired_at')
                ->label('Expired at')
                ->required()
                ->seconds(false)
                ->native(false)
                ->disabled($isLocked),

            Forms\Components\Toggle::make('is_expired')
                ->label('Is expired')
                ->required()
                ->inline(false)
                ->disabled($isLocked),

            Forms\Components\TextInput::make('priority_order')
                ->label('Priority order')
                ->required()
                ->numeric()
                ->default(0)
                ->disabled($isLocked),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('priority_order')
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('estimated_amount')
                    ->label('Est. Nominal')
                    ->money('IDR', true)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('difficulty_level')
                    ->label('Kesulitan')
                    ->badge()
                    ->formatStateUsing(fn($state) => Task::DIFFICULTIES[$state] ?? $state)
                    ->color(fn(Task $r) => $r->getDifficultyBadgeColorAttribute()),
                Tables\Columns\IconColumn::make('is_expired')->label('Expired?')->boolean(),
                Tables\Columns\TextColumn::make('expired_at')->label('Expired At')->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('priority_order')->label('Prioritas')->sortable(),
                Tables\Columns\TextColumn::make('activeUserTask.count')->label('Active Taken')->counts('activeUserTask'),
            ])
            ->filters([
                Tables\Filters\Filter::make('available')->label('Hanya Available')->query(fn(Builder $q) => $q->available()),
                Tables\Filters\TernaryFilter::make('expired')->label('Status Expired')->trueLabel('Sudah')->falseLabel('Belum')
                    ->queries(
                        true: fn(Builder $q) => $q->expired(),
                        false: fn(Builder $q) => $q->active(),
                        blank: fn(Builder $q) => $q
                    ),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Task')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Pastikan admin_id dan created_by selalu terisi dengan user yang sedang login
                        $currentUserId = Auth::id();

                        if (!$currentUserId) {
                            // Jika tidak ada user yang login, ambil admin pertama dari database
                            $adminUser = \App\Models\User::whereIn('role', ['admin', 'superadmin'])->first();
                            $currentUserId = $adminUser?->id ?? 1;
                        }

                        $data['admin_id'] = $currentUserId;
                        $data['created_by'] = $currentUserId;
                        $data['expired_at'] = $data['expired_at'] ?? now()->addDays(3);
                        return $data;
                    })
                    ->after(fn() => $this->js('window.location.reload()')),
                
                // Download CSV Template
                Action::make('download_template')
                    ->label('📄 Template CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(asset('sample_tasks.csv'))
                    ->openUrlInNewTab(),

                // Import CSV Action
                Action::make('import_csv')
                    ->label('📥 Import CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\Placeholder::make('template_info')
                            ->content(new \Illuminate\Support\HtmlString(
                                '<div class="text-sm p-3 bg-blue-50 border border-blue-200 rounded-lg">'
                                . '<p class="font-semibold text-blue-800 mb-1">📋 Format CSV:</p>'
                                . '<code class="text-xs text-blue-700">title, description, whatsapp_group_link, difficulty_level, estimated_amount, expired_at, priority_order</code>'
                                . '<p class="mt-2"><a href="' . asset('sample_tasks.csv') . '" download class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium underline">⬇️ Download Template CSV</a></p>'
                                . '</div>'
                            )),
                        FileUpload::make('csv_file')
                            ->label('File CSV')
                            ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'text/plain', '.csv'])
                            ->required()
                            ->disk('local')
                            ->directory('csv-imports')
                            ->visibility('private'),
                        FileUpload::make('vcf_zip')
                            ->label('File ZIP VCF (Opsional)')
                            ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed', '.zip'])
                            ->disk('local')
                            ->directory('vcf-imports')
                            ->visibility('private')
                            ->helperText('ZIP berisi file .vcf dengan nama sesuai nomor baris CSV (1.vcf, 2.vcf, 3.vcf, dst). Baris tanpa file VCF akan dilewati.'),
                    ])
                    ->action(function (array $data): void {
                        $categoryId = $this->getOwnerRecord()->id;
                        $currentUserId = Auth::id();

                        if (!$currentUserId) {
                            $adminUser = \App\Models\User::whereIn('role', ['admin', 'superadmin'])->first();
                            $currentUserId = $adminUser?->id ?? 1;
                        }

                        // Handle CSV file path
                        $filePath = $data['csv_file'];
                        if (is_array($filePath)) {
                            $filePath = reset($filePath);
                        }
                        $fullPath = Storage::disk('local')->path($filePath);
                        if (!file_exists($fullPath)) {
                            $fullPath = Storage::disk('public')->path($filePath);
                        }
                        if (!file_exists($fullPath)) {
                            Notification::make()->title('File CSV tidak ditemukan')->body('Path: ' . $filePath)->danger()->send();
                            return;
                        }

                        // Handle VCF ZIP file (optional)
                        $vcfTempDir = null;
                        $vcfFiles = [];
                        if (!empty($data['vcf_zip'])) {
                            $zipPath = $data['vcf_zip'];
                            if (is_array($zipPath)) {
                                $zipPath = reset($zipPath);
                            }
                            $zipFullPath = Storage::disk('local')->path($zipPath);

                            if (file_exists($zipFullPath)) {
                                $vcfTempDir = sys_get_temp_dir() . '/vcf_import_' . uniqid();
                                @mkdir($vcfTempDir, 0755, true);

                                $zip = new \ZipArchive();
                                if ($zip->open($zipFullPath) === true) {
                                    $zip->extractTo($vcfTempDir);
                                    $zip->close();

                                    // Scan extracted files for .vcf files
                                    $iterator = new \RecursiveIteratorIterator(
                                        new \RecursiveDirectoryIterator($vcfTempDir, \RecursiveDirectoryIterator::SKIP_DOTS)
                                    );
                                    foreach ($iterator as $file) {
                                        if (strtolower($file->getExtension()) === 'vcf') {
                                            $name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                                            if (is_numeric($name)) {
                                                $vcfFiles[(int) $name] = $file->getRealPath();
                                            }
                                        }
                                    }
                                }
                            }

                            Storage::disk('local')->delete($zipPath);
                        }

                        $handle = fopen($fullPath, 'r');
                        $header = fgetcsv($handle); // Skip header row

                        $successCount = 0;
                        $errorCount = 0;
                        $vcfCount = 0;
                        $errors = [];
                        $rowNumber = 0;

                        while (($row = fgetcsv($handle)) !== false) {
                            $rowNumber++;
                            try {
                                if (count($row) < 4) {
                                    $errorCount++;
                                    $errors[] = "Baris $rowNumber dilewati: kolom kurang dari 4";
                                    continue;
                                }

                                $taskData = [
                                    'category_id' => $categoryId,
                                    'admin_id' => $currentUserId,
                                    'created_by' => $currentUserId,
                                    'title' => $row[0] ?? '',
                                    'description' => $row[1] ?? '',
                                    'whatsapp_group_link' => $row[2] ?? '',
                                    'difficulty_level' => $row[3] ?? 'easy',
                                    'estimated_amount' => !empty($row[4]) ? (float)$row[4] : null,
                                    'expired_at' => !empty($row[5]) ? \Carbon\Carbon::parse($row[5]) : now()->addDays(3),
                                    'priority_order' => !empty($row[6]) ? (int)$row[6] : 0,
                                    'is_expired' => false,
                                ];

                                if (empty($taskData['title']) || empty($taskData['description']) || empty($taskData['whatsapp_group_link'])) {
                                    $errorCount++;
                                    $errors[] = "Baris $rowNumber dilewati: field wajib kosong (title/description/whatsapp_group_link)";
                                    continue;
                                }

                                // Check for matching VCF file from ZIP
                                if (isset($vcfFiles[$rowNumber])) {
                                    $vcfFileName = 'vcf-files/' . uniqid('vcf_') . '.vcf';
                                    Storage::disk('public')->put($vcfFileName, file_get_contents($vcfFiles[$rowNumber]));
                                    $taskData['vcf_data'] = $vcfFileName;
                                    $vcfCount++;
                                }

                                Task::create($taskData);
                                $successCount++;
                            } catch (\Exception $e) {
                                $errorCount++;
                                $errors[] = "Baris $rowNumber error: " . $e->getMessage();
                            }
                        }

                        fclose($handle);
                        Storage::disk('local')->delete($filePath);

                        // Cleanup temp dir
                        if ($vcfTempDir && is_dir($vcfTempDir)) {
                            $this->deleteDirectory($vcfTempDir);
                        }

                        if ($successCount > 0) {
                            $vcfInfo = $vcfCount > 0 ? ", $vcfCount file VCF terpasang" : '';
                            Notification::make()
                                ->title('Import Berhasil')
                                ->body("$successCount task berhasil diimport{$vcfInfo}" . ($errorCount > 0 ? ", $errorCount gagal" : ''))
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Import Gagal')
                                ->body('Tidak ada task yang berhasil diimport. ' . implode('; ', array_slice($errors, 0, 3)))
                                ->danger()
                                ->send();
                        }
                    })
                    ->modalHeading('Import Tasks dari CSV')
                    ->modalDescription('Upload CSV dan (opsional) ZIP berisi file VCF bernama 1.vcf, 2.vcf, dst sesuai baris CSV.'),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn(Task $r) => $r->canBeEdited())
                    ->mutateFormDataUsing(function (array $data): array {
                        // Pastikan admin_id tetap terisi saat edit
                        if (!isset($data['admin_id']) || !$data['admin_id']) {
                            $currentUserId = Auth::id();

                            if (!$currentUserId) {
                                $adminUser = \App\Models\User::whereIn('role', ['admin', 'superadmin'])->first();
                                $currentUserId = $adminUser?->id ?? 1;
                            }

                            $data['admin_id'] = $currentUserId;
                        }
                        return $data;
                    }),
                DeleteAction::make()->requiresConfirmation()->visible(fn(Task $r) => !$r->isTaken()),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn($records) => collect($records)->every(fn(Task $t) => !$t->isTaken())),
            ]);
    }

    /**
     * Recursively delete a directory and its contents.
     */
    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getRealPath());
            } else {
                @unlink($item->getRealPath());
            }
        }
        @rmdir($dir);
    }
}
