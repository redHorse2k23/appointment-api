<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('court_schedules', function (Blueprint $table) {
            // drop old column
            $table->dropColumn('date');
        });

        Schema::table('court_schedules', function (Blueprint $table) {
            // add new column
            $table->enum('day', [
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday'
            ])->after('court_id');
            $table->enum('status', [
                'available',
                'unavailable',
                'maintenance',
            ])->default('available');

            // add unique constraint
            $table->unique(['court_id', 'day']);
        });
    }
    public function down(): void
    {
        Schema::table('court_schedules', function (Blueprint $table) {
            $table->dropUnique(['court_id', 'day']);
            $table->dropColumn('day');
            $table->dropColumn('status');
            $table->date('date')->after('court_id');
        });
    }
};