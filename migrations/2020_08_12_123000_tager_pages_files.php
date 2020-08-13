<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TagerPagesFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tager_page_field_files', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('field_id');
            $table->unsignedBigInteger('file_id');

            $table->foreign('field_id')->references('id')->on('tager_page_fields')->onDelete('CASCADE');
            $table->foreign('file_id')->references('id')->on('files')->onDelete('CASCADE');

        });

        Schema::table('tager_page_fields', function (Blueprint $table) {
            $table->dropForeign('tager_page_fields_file_id_foreign');
            $table->dropColumn('file_id');

            $table->dropForeign('tager_page_fields_parent_id_foreign');
            $table->foreign('parent_id')->references('id')->on('tager_page_fields')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tager_page_field_files');

        Schema::table('tager_page_fields', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->nullable();

            $table->foreign('file_id')->references('id')->on('files');
        });
    }
}
