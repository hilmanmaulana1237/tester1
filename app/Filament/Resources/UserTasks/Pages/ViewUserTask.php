<?php

namespace App\Filament\Resources\UserTasks\Pages;

use App\Filament\Resources\UserTasks\UserTaskResource;
use App\Filament\Resources\UserTasks\Widgets\TaskChatWidget;
use App\Models\UserTask;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserTask extends ViewRecord
{
    protected static string $resource = UserTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            TaskChatWidget::class,
        ];
    }
}
