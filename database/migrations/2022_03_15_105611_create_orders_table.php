<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('device_id');
            $table->integer('quantity')->default(1);
            $table->integer('sum');
            $table->unsignedBigInteger('state_id')->default(1);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('device_id')->references('id')->on('devices')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('state_id')->references('id')->on('order_states')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
