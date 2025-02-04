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
        Schema::create('ftp_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapping_id')->references('id')->on('csv_mappings')->onDelete('cascade');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('domain')->nullable();
            $table->string('directory')->nullable();
            $table->string('quota')->default('limited');
            $table->string('quota_limit')->default('1000');
            $table->string('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ftp_accounts');
    }
};
