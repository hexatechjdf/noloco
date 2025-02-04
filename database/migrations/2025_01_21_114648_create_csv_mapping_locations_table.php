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
        Schema::create('csv_mapping_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapping_id')->references('id')->on('csv_mappings')->onDelete('cascade');
            $table->foreignId('account_id')->references('id')->on('ftp_accounts')->onDelete('cascade');
            $table->string('location_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_mapping_locations');
    }
};
