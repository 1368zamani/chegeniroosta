<?php

namespace App\Console\Commands;

use App\Models\Section;
use Illuminate\Console\Command;

class SetBakhshdarCommand extends Command
{
    protected $signature = 'section:bakhshdar {slug}';
    protected $description = 'Set bakhshdar info for a section';

    public function handle(): int
    {
        $slug = $this->argument('slug');
        $section = Section::where('slug', $slug)->first();

        if (!$section) {
            $this->error("Section {$slug} not found!");
            return 1;
        }

        $this->info("Editing bakhshdar for: {$section->name}");

        $name = $this->ask('Name', $section->bakhshdar_name);
        $bio = $this->ask('Bio', $section->bakhshdar_bio);
        $phone = $this->ask('Phone', $section->bakhshdar_phone);
        $email = $this->ask('Email', $section->bakhshdar_email);

        $section->bakhshdar_name = $name;
        $section->bakhshdar_bio = $bio;
        $section->bakhshdar_phone = $phone;
        $section->bakhshdar_email = $email;
        $section->save();

        $this->info('Saved!');
        return 0;
    }
}
