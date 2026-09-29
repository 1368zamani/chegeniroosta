<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    protected $fillable = [
        'section_id',
        'name',
        'slug',
        'subdomain',
        'county',
        'section',
        'district',
        'province',
        'description',
        'address',
        'phone',
        'email',
        'population',
        'households',
        'area',
        'dehyar_name',
        'council_head_name',
        'logo',
        'cover_image',
        'is_active',
        'welcome_text',
        'history_text',
        'naming_story',
        'distance_to_center',
        'hectares',
        'latitude',
        'longitude',
        'has_school',
        'has_health_house',
        'has_mosque',
        'has_water',
        'has_gas',
        'has_electricity',
        'has_internet',
        'has_sport_field',
        'council_phone',
        'council_members_count',
        'dehyar_hours',
        'bank_account',
        'bank_name',
        'payment_id',
        'dehyar_photo',
        'show_stats',
        'show_news',
        'show_projects',
        'show_events',
        'show_facilities',
        'show_council',
        'show_attractions',
        'show_gallery',
        'show_request_box',
        'show_about',
        'show_info',];



    protected $casts = [
        'show_stats' => 'boolean',
        'show_news' => 'boolean',
        'show_projects' => 'boolean',
        'show_events' => 'boolean',
        'show_facilities' => 'boolean',
        'show_council' => 'boolean',
        'show_attractions' => 'boolean',
        'show_gallery' => 'boolean',
        'show_request_box' => 'boolean',
        'show_about' => 'boolean',
        'show_info' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function images()
    {
        return $this->hasMany(VillageImage::class);
    }

    public function news()
    {
        return $this->hasMany(VillageNews::class);
    }

    public function contact()
    {
        return $this->hasOne(VillageContact::class);
    }

    public function villageNews(): HasMany
    {
        return $this->hasMany(VillageNews::class);
    }

    public function facilities()
    {
        return $this->hasMany(VillageFacility::class)->orderBy('sort_order');
    }

    public function councilMembers()
    {
        return $this->hasMany(VillageCouncilMember::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function attractions()
    {
        return $this->hasMany(VillageAttraction::class)->orderBy('sort_order');
    }

    public function projects()
    {
        return $this->hasMany(VillageProject::class)->orderBy('sort_order');
    }

    public function requests()
    {
        return $this->hasMany(VillageRequest::class);
    }

    public function events()
    {
        return $this->hasMany(VillageEvent::class)->orderBy('event_date');
    }

    public function upcomingEvents()
    {
        return $this->hasMany(VillageEvent::class)->where('event_date', '>=', now()->toDateString())->orderBy('event_date')->limit(10);
    }


    public function councilResolutions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\VillageCouncilResolution::class)->orderBy('sort_order')->orderBy('session_date', 'desc');
    }

}
