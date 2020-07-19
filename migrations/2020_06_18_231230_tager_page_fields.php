<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TagerPageFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tager_page_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');

            $table->string('field');
            $table->longText('value')->nullable();
            $table->unsignedBigInteger('file_id')->nullable();

            $table->index('page_id');

            $table->foreign('page_id')->references('id')->on('tager_pages');
            $table->foreign('file_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tager_page_fields');
    }
}
