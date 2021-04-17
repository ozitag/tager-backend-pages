<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TagerPagesRemoveOg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tager_pages', function (Blueprint $table) {
            $table->dropColumn('open_graph_title');
            $table->dropColumn('open_graph_description');
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
            $table->string('open_graph_title')->nullable();
            $table->text('open_graph_description')->nullable();
        });
    }
}
