<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('currency_rates', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('currency_from_id')->unsigned();
            $table->bigInteger('currency_to_id')->unsigned();
            $table->double('rate');
            $table->timestamps();

            $table->foreign('currency_from_id')
                ->references('id')->on('currencies')
                ->onDelete('cascade');

            $table->foreign('currency_to_id')
                ->references('id')->on('currencies')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_rates');
        Schema::dropIfExists('currencies');
    }
}
