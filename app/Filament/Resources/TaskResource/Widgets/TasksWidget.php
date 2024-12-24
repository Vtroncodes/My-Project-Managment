<?php

namespace App\Filament\Resources\TaskResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\Task;

class TasksWidget extends Widget
{
    public $record;
    protected static string $view = 'filament.resources.task-resource.widgets.tasks-widget';
    public function mount($record): void
    {
        $this->record = $record;
    }

    public function getWorkLogs()
    {
        return Task::where('project_id', $this->record)->get();
    }
}
