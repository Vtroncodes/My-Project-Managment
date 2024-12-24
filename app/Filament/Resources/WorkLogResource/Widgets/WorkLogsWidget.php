<?php

namespace App\Filament\Resources\WorkLogResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\WorkLog;

class WorkLogsWidget extends Widget
{
    public $record;

    protected static string $view = 'filament.resources.work-log-resource.widgets.work-logs-widget';

    public function mount($record): void
    {
        $this->record = $record;
    }

    public function getWorkLogs()
    {
        return WorkLog::where('project_id', $this->record)->get();
    }
}
