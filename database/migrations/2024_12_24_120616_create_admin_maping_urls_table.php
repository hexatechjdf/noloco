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
        Schema::create('admin_maping_urls', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->nullable();
            $table->string('url');
            $table->text('attributes');
            $table->longText('mapping')->nullable();
            $table->text('searchable_fields')->nullable();
            $table->string('table')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_maping_urls');
    }
};
