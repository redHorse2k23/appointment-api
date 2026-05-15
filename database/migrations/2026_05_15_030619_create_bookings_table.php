<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id_1');
            $table->unsignedBigInteger('court_id');
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes');
            $table->decimal('amount', 8, 2);
            $table->string('status')->enum(['pending', 'confirmed', 'cancelled']);
            $table->string('payment_method')->enum(['credit_card', 'paypal', 'cash']);
            $table->string('transaction_id')->nullable();
            $table->string('attachment')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('decline_by')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->string('cancelled_by')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('rescheduled_by')->nullable();
            $table->timestamp('rescheduled_at')->nullable();
            $table->string('reschedule_reason')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->string('admin_notes')->nullable(); 
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
