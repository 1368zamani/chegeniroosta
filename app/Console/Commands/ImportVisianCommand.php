<?php

namespace App\Console\Commands;

use App\Models\Section;
use App\Models\Village;
use Illuminate\Console\Command;

class ImportVisianCommand extends Command
{
    protected $signature = 'import:visian {--dry-run}';
    protected $description = 'Import Visian villages from JSON';

    public function handle(): int
    {
        $path = storage_path('imports/visian_villages.json');
        if (!file_exists($path)) {
            $this->error("File not found: $path");
            return 1;
        }

        $data = json_decode(file_get_contents($path), true);
        $villages = $data['villages'] ?? [];

        $section = Section::where('slug', 'visian')->first();
        if (!$section) {
            $this->error('Section visian not found!');
            return 1;
        }

        $dryRun = $this->option('dry-run');
        $created = 0;
        $updated = 0;

        foreach ($villages as $v) {
            $existing = Village::where('slug', $v['slug'])->first();

            if (!$existing) {
                $existing = Village::where('name', $v['name'])->first();
            }

            $action = $existing ? 'UPDATE' : 'CREATE';
            if ($dryRun) {
                $this->line("[$action] {$v['name']} → {$v['slug']}");
                continue;
            }

            $payload = [
                'name'         => $v['name'],
                'slug'         => $v['slug'],
                'section_id'   => $section->id,
                'section'      => $section->name,
                'district' => $v['dehestan'] ?? 'ویسیان',
                'county'       => 'چگنی',
                'province'     => 'لرستان',
                'dehyar_name'  => $v['dehyar'] ?? null,
                'phone'        => $v['phone'] ?? null,
                'population'   => $v['population'] ?? 0,
                'households'   => $v['households'] ?? 0,
                'is_active'    => true,
            ];

            if ($existing) {
                $existing->update($payload);
                $updated++;
            } else {
                Village::create($payload);
                $created++;
            }
        }

        $this->info('');
        $this->info("Created: $created");
        $this->info("Updated: $updated");
        $this->info("Total: " . count($villages));

        return 0;
    }
}
