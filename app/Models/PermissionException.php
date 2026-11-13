<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PermissionExceptionMode;
use Database\Factories\PermissionExceptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Permission;

/**
 * A per-person override of a permission. `revoke` removes a permission that a
 * role would otherwise grant; `grant` adds a permission the roles do not grant.
 * A reason is mandatory and every change is written to the activity log.
 *
 * @mixin Model
 */
class PermissionException extends Model
{
    /** @use HasFactory<PermissionExceptionFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'permission_id',
        'mode',
        'reason',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (! $model->getAttribute('created_by') && auth()->id()) {
                $model->setAttribute('created_by', auth()->id());
            }
        });

        static::saving(function (self $model): void {
            if (blank($model->getAttribute('reason'))) {
                throw new \InvalidArgumentException('Alasan pengecualian izin wajib diisi.');
            }
        });

        static::created(function (self $model): void {
            self::logActivity('created', $model);
        });
        static::updated(function (self $model): void {
            self::logActivity('updated', $model);
        });
        static::deleted(function (self $model): void {
            self::logActivity('deleted', $model);
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'mode' => PermissionExceptionMode::class,
        ];
    }

    private static function logActivity(string $event, self $model): bool
    {
        $mode = $model->getAttribute('mode');

        activity('platform')
            ->performedOn($model)
            ->event($event)
            ->withProperties([
                'user_id' => $model->getAttribute('user_id'),
                'permission_id' => $model->getAttribute('permission_id'),
                'mode' => $mode instanceof PermissionExceptionMode ? $mode->value : (is_scalar($mode) ? (string) $mode : null),
                'reason' => $model->getAttribute('reason'),
                'created_by' => $model->getAttribute('created_by'),
            ])
            ->log('permission_exception.'.$event);

        return true;
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Permission, $this> */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
