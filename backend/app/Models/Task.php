<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'priority',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'status'   => 'string',
        'priority' => 'string',
    ];

    const STATUS_PENDING     = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_DONE        = 'done';

    const PRIORITY_LOW    = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH   = 'high';

    public static array $validTransitions = [
        self::STATUS_PENDING     => self::STATUS_IN_PROGRESS,
        self::STATUS_IN_PROGRESS => self::STATUS_DONE,
    ];

    public static array $prioritySortOrder = [
        self::PRIORITY_HIGH,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_LOW,
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        $validNext = self::$validTransitions[$this->status] ?? null;
        return $validNext === $newStatus;
    }

    public function canBeDeleted(): bool
    {
        return $this->status === self::STATUS_DONE;
    }
}