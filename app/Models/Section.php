<?php

namespace App\Models;

use App\Models\Concerns\HasStats;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasStats;

    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'county',
        'district',
        'province',
        'description',
        'address',
        'phone',
        'email',
        'logo',
        'cover_image',
        'is_active',
        'bakhshdar_name',
        'bakhshdar_photo',
        'bakhshdar_bio',
        'bakhshdar_phone',
        'bakhshdar_email',
        'bakhshdar_education',
        'bakhshdar_experience',
    ];

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
    }

    public function sectionNews(): HasMany
    {
        return $this->hasMany(SectionNews::class);
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(SectionGallery::class)->orderBy('sort_order')->orderByDesc('id');
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->logo)) {
            return asset('storage/' . $this->logo);
        }
        return asset('images/gov/emblem.png');
    }

    public function getGalleryImagesCountAttribute(): int
    {
        if (class_exists('App\\Models\\SectionGallery')) {
            return \App\Models\SectionGallery::where('section_id', $this->id)->count();
        }
        return 0;
    }


    public function councilMembers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\SectionCouncilMember::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

}
