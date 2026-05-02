<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Task;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->required(),
                Select::make('admin_id')
                    ->label('Admin')
                    ->relationship('admin', 'name')
                    ->required(),
                TextInput::make('title')
                    ->label('Title')
                    ->required(),
                FileUpload::make('vcf_data')
                    ->label('File VCF (Kontak)')
                    ->acceptedFileTypes(['text/vcard', 'text/x-vcard', '.vcf'])
                    ->disk('public')
                    ->directory('vcf-files')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->helperText('Upload file .vcf berisi data kontak. User bisa download file ini dari halaman tugas.')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('whatsapp_group_link')
                    ->label('Whatsapp group link')
                    ->required(),
                TextInput::make('tutorial_link')
                    ->label('Link panduan/tutorial')
                    ->placeholder('https://contoh.com/panduan')
                    ->url()
                    ->helperText('Tautan panduan yang akan ditampilkan di wizard pengguna.')
                    ->columnSpanFull(),
                Select::make('difficulty_level')
                    ->label('Difficulty level')
                    ->options(Task::DIFFICULTIES)
                    ->required()
                    ->default('easy'),
                TextInput::make('estimated_amount')
                    ->label('Estimasi Nominal (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('Contoh: 50000')
                    ->helperText('Perkiraan bayaran untuk task ini. User akan melihat nominal ini.')
                    ->columnSpanFull(),
                DateTimePicker::make('expired_at')
                    ->label('Expired at')
                    ->required(),
                Toggle::make('is_expired')
                    ->label('Is expired')
                    ->required(),
                TextInput::make('priority_order')
                    ->label('Priority order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}

