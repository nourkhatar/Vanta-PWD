<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique()->after('name');
        });

        DB::statement('UPDATE users SET email = name WHERE email IS NULL OR email = ""');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
        });

        DB::statement('UPDATE users SET name = email WHERE name IS NULL OR name = ""');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
