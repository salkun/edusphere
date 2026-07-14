<?php

namespace App\Console\Commands;

use App\Services\JejakmuSyncService;
use Illuminate\Console\Command;

class SyncJejakmuUsers extends Command
{
    /**
     * @var string
     */
    protected $signature = 'users:sync-jejakmu';

    /**
     * @var string
     */
    protected $description = 'Sinkronisasi data user dari API Jejakmu ke database Edusphere';

    public function handle(JejakmuSyncService $syncService): int
    {
        $this->info('Memulai sinkronisasi user dari Jejakmu...');

        try {
            $result = $syncService->sync();
        } catch (\Throwable $exception) {
            $this->error('Sinkronisasi gagal: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->table(
            ['Metric', 'Value'],
            [
                ['Halaman', $result['pages']],
                ['User baru', $result['created']],
                ['User diperbarui', $result['updated']],
                ['Total diproses', $result['total']],
            ]
        );

        $this->info('Sinkronisasi selesai.');

        return self::SUCCESS;
    }
}
