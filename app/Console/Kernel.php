<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\CleanOldInventoryRecords;
use App\Jobs\ProcessPendingSales;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        die('Kernel loaded!');
        //Elimina registros antigos do inventário
        $schedule->job(new CleanOldInventoryRecords)->everyMinute()->withoutOverlapping();

        //Processa estoque pending de sales->status = pending (lança registro em inventories)
        $schedule->job(new ProcessPendingSales)->everyMinute()->withoutOverlapping();
        
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}