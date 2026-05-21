<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'event_date', 'notes'];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = [];
        if ($this->event_date) {
            $parts[] = $this->event_date->format('d.m.Y');
        }
        if ($this->location) {
            $parts[] = $this->location;
        }
        $parts[] = '(' . $this->name . ')';
        return implode(' - ', $parts);
    }
}
