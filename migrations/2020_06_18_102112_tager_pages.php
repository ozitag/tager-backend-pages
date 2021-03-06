<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TagerPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tager_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('template')->nullable();
            $table->string('url_path');

            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();

            $table->unsignedBigInteger('image_id')->nullable();

            $table->string('page_title')->nullable();
            $table->text('page_description')->nullable();
            $table->string('open_graph_title')->nullable();
            $table->text('open_graph_description')->nullable();
            $table->unsignedBigInteger('open_graph_image_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('url_path');

            $table->foreign('parent_id')->references('id')->on('tager_pages');
            $table->foreign('image_id')->references('id')->on('files');
            $table->foreign('open_graph_image_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tager_pages');
    }
}
