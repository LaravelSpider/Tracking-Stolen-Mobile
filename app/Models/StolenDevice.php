<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class StolenDevice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'imei',
        'serial_number',
        'brand',
        'model',
        'color',
        'status',
        'lost_date',
        'description',
        'loss_location',
        'latitude',
        'longitude',
        'reported_by',
        'assigned_to',
        'found_at',
        'notes',
        'images',
        'reward_amount',
        'contact_info',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'lost_date' => 'date',
            'found_at' => 'datetime',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'images' => 'array',
            'reward_amount' => 'decimal:2',
            'priority' => 'integer',
        ];
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'device_id');
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByImei(Builder $query, string $imei): Builder
    {
        return $query->where('imei', $imei);
    }

    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeHighPriority(Builder $query): Builder
    {
        return $query->where('priority', '>=', 3);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'missing' => 'red',
            'reported' => 'yellow',
            'investigating' => 'blue',
            'found' => 'green',
            default => 'gray'
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            1 => 'Low',
            2 => 'Medium',
            3 => 'High',
            4 => 'Critical',
            default => 'Normal'
        };
    }
}
