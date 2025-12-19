<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Dataset;

class MigrateDatasetFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datasets:migrate-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memindahkan file dataset lama dari storage/app/public/datasets ke storage/app/datasets dan menyesuaikan path di database.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai migrasi file dataset lama...');

        // Ambil semua dataset yang punya file_path
        $datasets = Dataset::whereNotNull('file_path')->get();

        $migrated = 0;
        $skipped  = 0;

        foreach ($datasets as $dataset) {
            $oldPath = $dataset->file_path;

            // Kalau path sudah mengarah ke lokasi baru (storage/app/datasets), lewati
            if (Storage::exists($oldPath)) {
                $this->line("[SKIP] Dataset ID {$dataset->id} sudah di lokasi baru: {$oldPath}");
                $skipped++;
                continue;
            }

            $publicPath = 'public/' . ltrim($oldPath, '/');

            if (!Storage::exists($publicPath)) {
                $this->warn("[MISS] Dataset ID {$dataset->id} tidak menemukan file: {$publicPath}");
                $skipped++;
                continue;
            }

            $newPath = 'datasets/' . basename($oldPath);

            // Kalau sudah ada file dengan nama yang sama di lokasi baru, jangan timpa
            if (Storage::exists($newPath)) {
                $this->warn("[EXISTS] File tujuan sudah ada untuk Dataset ID {$dataset->id}: {$newPath}");
                $skipped++;
                continue;
            }

            // Pindahkan file dari public/datasets ke datasets
            $stream = Storage::readStream($publicPath);
            Storage::put($newPath, stream_get_contents($stream));
            fclose($stream);

            // Opsional: hapus file lama
            Storage::delete($publicPath);

            // Update path di database
            $dataset->file_path = $newPath;
            $dataset->save();

            $this->info("[OK] Dataset ID {$dataset->id} dipindah ke {$newPath}");
            $migrated++;
        }

        $this->info("Selesai. Migrated: {$migrated}, Skipped: {$skipped}");

        return self::SUCCESS;
    }
}
