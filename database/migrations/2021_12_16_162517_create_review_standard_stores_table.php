<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewStandardStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_standard_stores', function (Blueprint $table) {
            $table->id();
            $table->string('standard_store_category');
            $table->boolean('standard_store_min_product')->default(false);
            $table->boolean('standard_store_min_quantity')->default(false);
            $table->boolean('standard_store_term_and_condition')->default(false);
            $table->string('standard_store_product_photo_path');
            $table->string('standard_store_product_photo_name', 225);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_standard_kycs');
    }
}
