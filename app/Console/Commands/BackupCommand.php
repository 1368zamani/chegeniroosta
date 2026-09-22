<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use ZipArchive;

class BackupCommand extends Command
{
    protected $signature = 'app:backup {--keep=7 : تعداد بکاپ‌هایی که نگه داشته می‌شود}';
    protected $description = 'بکاپ کامل از دیتابیس و فایل‌های آپلودشده';

    public function handle(): int
    {
        $backupDir = storage_path('backups/auto');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $this->info("شروع بکاپ: {$timestamp}");

        // ---- ۱. بکاپ دیتابیس SQLite ----
        $dbPath = database_path('database.sqlite');
        if (file_exists($dbPath)) {
            $dbBackup = "{$backupDir}/db_{$timestamp}.sqlite";
            copy($dbPath, $dbBackup);
            $this->info("✅ دیتابیس: " . $this->size($dbBackup));
        } else {
            $this->warn("⚠ دیتابیس SQLite پیدا نشد");
        }

        // ---- ۲. بکاپ فایل‌های storage/app/public ----
        $storagePath = storage_path('app/public');
        if (is_dir($storagePath)) {
            $zipPath = "{$backupDir}/storage_{$timestamp}.zip";
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $this->addDirToZip($zip, $storagePath);
                $zip->close();
                $this->info("✅ storage: " . $this->size($zipPath));
            }
        }

        // ---- ۳. پاکسازی بکاپ‌های قدیمی ----
        $keep = (int) $this->option('keep');
        $this->cleanupOldBackups($backupDir, $keep);

        $this->info("🎉 بکاپ کامل شد.");
        $this->info("مسیر: {$backupDir}");

        return 0;
    }

    private function addDirToZip(ZipArchive $zip, string $dir, string $baseDir = ''): void
    {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $filePath = $dir . '/' . $file;
            $localPath = $baseDir ? $baseDir . '/' . $file : $file;

            if (is_dir($filePath)) {
                $zip->addEmptyDir($localPath);
                $this->addDirToZip($zip, $filePath, $localPath);
            } else {
                $zip->addFile($filePath, $localPath);
            }
        }
    }

    private function cleanupOldBackups(string $dir, int $keep): void
    {
        $files = glob($dir . '/*');
        if (!$files) return;

        usort($files, fn($a, $b) => filemtime($a) <=> filemtime($b));

        // گروه‌بندی بر اساس تاریخ
        $groups = [];
        foreach ($files as $f) {
            $date = date('Y-m-d', filemtime($f));
            $groups[$date][] = $f;
        }

        // فقط آخرین $keep روز رو نگه‌دار
        $dates = array_keys($groups);
        if (count($dates) <= $keep) return;

        $toDelete = array_slice($dates, 0, count($dates) - $keep);
        $deleted = 0;
        foreach ($toDelete as $date) {
            foreach ($groups[$date] as $file) {
                @unlink($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("🧹 {$deleted} فایل قدیمی پاک شد");
        }
    }

    private function size(string $path): string
    {
        $bytes = filesize($path);
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
