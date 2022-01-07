<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileKYCSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_k_y_c_s', function (Blueprint $table) {
            $table->id();
            $table->string('kyc_name');
            $table->string('kyc_ktp', 16);
            $table->text('kyc_address');
            $table->string('kyc_city', 45);
            $table->string('kyc_postal_code',5);
            $table->string('kyc_place_of_birth', 45);
            $table->dateTime('kyc_date_of_birth');
            $table->enum('kyc_gender', ['Laki-laki', 'Perempuan']);
            $table->string('kyc_province');
            $table->integer('kyc_province_id');
            $table->integer('kyc_city_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('kyc_is_approved')->default(false);
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
        Schema::dropIfExists('profile_k_y_c_s');
    }
}
