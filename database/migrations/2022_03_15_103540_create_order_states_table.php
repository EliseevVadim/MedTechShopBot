<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOrderStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_states', function (Blueprint $table) {
            $table->id();
            $table->string('state_name');
            $table->timestamps();
        });

        $data = [
            ['state_name' => 'В корзине'],
            ['state_name' => 'Оплачено']
        ];

        DB::table('order_states')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_states');
    }
}
