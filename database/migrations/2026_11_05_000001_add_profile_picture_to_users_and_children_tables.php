<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'profile_picture')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('profile_picture')->nullable()->after('role');
            });
        }

        if (Schema::hasTable('children') && !Schema::hasColumn('children', 'profile_picture')) {
            Schema::table('children', function (Blueprint $table) {
                $table->string('profile_picture')->nullable()->after('first_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'profile_picture')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('profile_picture');
            });
        }

        if (Schema::hasTable('children') && Schema::hasColumn('children', 'profile_picture')) {
            Schema::table('children', function (Blueprint $table) {
                $table->dropColumn('profile_picture');
            });
        }
    }
};
