<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThumbnailPublicIdToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'thumbnail_public_id')) {
                $table->string('thumbnail_public_id')->nullable()->after('thumbnail_url');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'thumbnail_public_id')) {
                $table->dropColumn('thumbnail_public_id');
            }
        });
    }
}