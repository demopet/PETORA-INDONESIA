<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('active_branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
            $table->string('phone', 32)->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('active_branch_id');
            $table->dropColumn(['phone', 'is_active']);
        });
    }
};
