<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admin_maping_urls', function (Blueprint $table) {
            $table->text('displayable_fields')->nullable();
            $table->text('listed_attributes')->nullable();
            $table->text('related_urls')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_maping_urls', function (Blueprint $table) {
            $table->dropColumn(['displayable_fields', 'listed_attributes', 'related_urls']);
        });
    }
};
