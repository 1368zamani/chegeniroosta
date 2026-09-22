<?php

namespace App\Console\Commands;

use App\Models\Section;
use App\Models\Village;
use Illuminate\Console\Command;

class ImportSectionCommand extends Command
{
    protected $signature = 'import:section {slug} {--dry-run}';
    protected $description = 'Import villages for a specific section';

    public function handle(): int
    {
        $sectionSlug = $this->argument('slug');
        $path = storage_path("imports/{$sectionSlug}_villages.json");

        if (!file_exists($path)) {
            $this->error("File not found: $path");
            return 1;
        }

        $section = Section::where('slug', $sectionSlug)->first();
        if (!$section) {
            $this->error("Section {$sectionSlug} not found!");
            return 1;
        }

        $data = json_decode(file_get_contents($path), true);
        $villages = $data['villages'] ?? [];
        $dryRun = $this->option('dry-run');

        $created = 0;
        $updated = 0;

        foreach ($villages as $v) {
            $existing = Village::where('slug', $v['slug'])->first();
            if (!$existing) {
                $existing = Village::where('name', $v['name'])
                    ->where('section_id', $section->id)
                    ->first();
            }

            if ($dryRun) {
                $action = $existing ? 'UPDATE' : 'CREATE';
                $this->line("[$action] {$v['name']} -> {$v['slug']}");
                continue;
            }

            $payload = [
                'name'         => $v['name'],
                'slug'         => $v['slug'],
                'section_id'   => $section->id,
                'section'      => $section->name,
                'district'     => $v['dehestan'],
                'county'       => $v['county'],
                'province'     => $v['province'],
                'population'   => $v['population'],
                'households'   => $v['households'],
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
