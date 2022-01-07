<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('store_name');
            $table->text('store_description');
            $table->string('store_province', 225);
            $table->string('store_city', 225);
            $table->string('store_postal_code', 6);
            $table->text('store_complete_address');
            $table->text('store_image_profile');
            $table->string('store_image_name', 225);
            $table->bigInteger('store_balance')->default(0);
            $table->bigInteger('store_total_revenue')->default(0);
            $table->bigInteger('store_total_withdrawals')->default(0);
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
        Schema::dropIfExists('stores');
    }
}
