<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PermissionExceptionMode;
use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;
    use InteractsWithAppAuthentication;
    use InteractsWithAppAuthenticationRecovery;
    use LogsActivity;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'active_branch_id',
        'is_active',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('platform')
            ->logOnly(['name', 'email', 'phone', 'active_branch_id', 'is_active'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    /** @return BelongsTo<Branch, $this> */
    public function activeBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'active_branch_id');
    }

    /** @return BelongsToMany<Branch, $this> */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class)->withTimestamps()->withPivot(['is_primary', 'meta']);
    }

    /** @return HasMany<PermissionException, $this> */
    public function permissionExceptions(): HasMany
    {
        return $this->hasMany(PermissionException::class);
    }

    /**
     * Resolve permissions while honoring per-person exceptions.
     *
     * Spatie's Gate integration calls checkPermissionTo() for every ability, so
     * a revoke exception denies the permission even when a role grants it, and a
     * grant exception allows it even when no role grants it.
     *
     * @param  mixed  $permission
     */
    public function checkPermissionTo($permission, ?string $guardName = null): bool
    {
        $permissionId = $this->resolveExceptionPermissionId($permission, $guardName);

        if ($permissionId !== null) {
            $mode = PermissionException::query()
                ->where('user_id', $this->getKey())
                ->where('permission_id', $permissionId)
                ->toBase()
                ->value('mode');

            if ($mode === PermissionExceptionMode::Revoke->value) {
                return false;
            }

            if ($mode === PermissionExceptionMode::Grant->value) {
                return true;
            }
        }

        try {
            return $this->hasPermissionTo($permission, $guardName);
        } catch (PermissionDoesNotExist) {
            return false;
        }
    }

    private function resolveExceptionPermissionId(mixed $permission, ?string $guardName): ?int
    {
        try {
            if ($permission instanceof Permission) {
                return (int) $permission->getKey();
            }

            if ($permission instanceof \BackedEnum) {
                $permission = $permission->value;
            }

            if (is_int($permission)) {
                return $permission;
            }

            if (is_string($permission) && $permission !== '') {
                return (int) Permission::findByName($permission, $guardName ?? $this->getDefaultGuardName())->getKey();
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }
}
