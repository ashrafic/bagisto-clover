<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clover_checkout_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('checkout_session_id')->unique();
            $table->integer('cart_id')->unsigned();
            $table->string('status')->default('new');
            $table->string('payment_id')->nullable();
            $table->decimal('base_grand_total', 12)->default(0);
            $table->string('currency_code', 10)->nullable();
            $table->string('verified_via')->nullable();
            $table->timestamps();

            $table->foreign('cart_id')->references('id')->on('cart')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clover_checkout_sessions');
    }
};
