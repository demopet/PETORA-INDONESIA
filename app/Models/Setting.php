<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Setting extends Model
{
    use LogsActivity;

    /** @var list<string> */
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('platform')
            ->logOnly(['group', 'key', 'value', 'type'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
