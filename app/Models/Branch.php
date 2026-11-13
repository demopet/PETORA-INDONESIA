<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Branch extends Model
{
    use HasFactory;
    use LogsActivity;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'timezone',
        'is_active',
        'receipt_header',
        'receipt_footer',
        'settings',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('platform')
            ->logOnly([
                'name',
                'code',
                'address',
                'phone',
                'email',
                'timezone',
                'is_active',
                'receipt_header',
                'receipt_footer',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /** @return BelongsToMany<User, $this> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot(['is_primary', 'meta']);
    }
}
