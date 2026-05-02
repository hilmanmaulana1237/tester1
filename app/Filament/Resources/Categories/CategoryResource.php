<?php

namespace App\Filament\Resources\Categories;

use BackedEnum;
use UnitEnum;
use App\Models\Category;
use App\Models\User;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\Pages\ViewCategory;
use App\Filament\Resources\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Categories\Tables\CategoriesTable;
use App\Filament\Resources\Categories\RelationManagers\TasksRelationManager;
use App\Filament\Resources\Categories\RelationManagers\UserTasksRelationManager;
use App\Filament\Resources\Categories\RelationManagers\TaskChatsRelationManager;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Kategori';

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategori';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    // Use the actual DB column name for title used by Filament's global search
    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Filter query based on user role.
     * Admin biasa hanya bisa melihat category yang dia buat.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Superadmin bisa lihat semua
        if ($user->role === User::ROLE_SUPERADMIN) {
            return $query;
        }

        // Admin biasa hanya bisa lihat category yang dia buat
        return $query->where('created_by', $user->id);
    }

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class,
            UserTasksRelationManager::class,
            TaskChatsRelationManager::class, // [NEW] Added TaskChatsRelationManager
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'view' => ViewCategory::route('/{record}'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
