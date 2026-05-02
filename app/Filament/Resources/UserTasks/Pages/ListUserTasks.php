<?php

namespace App\Filament\Resources\UserTasks\Pages;

use App\Filament\Resources\UserTasks\UserTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserTasks extends ListRecords
{
    protected static string $resource = UserTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Removed create action as UserTasks should be created through the user interface
        ];
    }
}
