<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPolicyAndEtcToCourtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courts', function (Blueprint $table) {
            $table->string('policy')->nullable();
            $table->string('description')->nullable();
            $table->dropUnique(['court_number']);
        });


        DB::statement("
            ALTER TABLE courts
            MODIFY status ENUM(
                'maintenance',
                'available',
                'closed'
            ) NOT NULL DEFAULT 'closed'
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courts', function (Blueprint $table) {
            $table->dropColumn(['policy', 'description']);
        });
    }
}
