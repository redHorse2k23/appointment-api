<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('booking_id');
            $table->decimal('amount', 8, 2);
            $table->string('payment_method')->enum(['credit_card', 'paypal', 'cash']);
            $table->decimal('fee', 8, 2)->default(0.00);
            $table->string('reference_number')->unique();  
            $table->string('type')->enum(['payment', 'refund', 'adjustment']);
            $table->string('method')->enum(['credit_card', 'paypal', 'cash']);
            $table->decimal('net_amount', 8, 2)->default(0.00);
            $table->string('status')->enum(['pending', 'completed', 'failed', 'refunded']);
            $table->timestamp('processed_at')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
