<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TagerPagesNested extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tager_pages', function (Blueprint $table) {
            $table->dropForeign('tager_pages_parent_id_foreign');
            $table->dropColumn('parent_id');
        });

        Schema::table('tager_pages', function (Blueprint $table) {
            $table->nestedSet();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tager_pages', function (Blueprint $table) {
            $table->dropNestedSet();
        });

        Schema::table('tager_pages', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('tager_pages');
        });
    }
}
