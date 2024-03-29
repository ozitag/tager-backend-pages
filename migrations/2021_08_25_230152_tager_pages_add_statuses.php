<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TagerPagesAddStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tager_pages', function (Blueprint $table) {
            $table->string('status')->after('id')->default('PUBLISHED');
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
            $table->dropColumn('status');
        });
    }
}
