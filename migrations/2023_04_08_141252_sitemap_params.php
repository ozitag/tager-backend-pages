<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tager_pages', function(Blueprint $table){
            $table->float('sitemap_priority')->after('open_graph_image_id')->nullable();
            $table->string('sitemap_frequency')->after('sitemap_priority')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tager_pages', function(Blueprint $table){
            $table->dropColumn('sitemap_priority');
            $table->dropColumn('sitemap_frequency');
        });
    }
};
