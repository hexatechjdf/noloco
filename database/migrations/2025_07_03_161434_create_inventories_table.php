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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('dealer_id')->nullable();
            $table->string('location_id')->nullable();
            $table->integer('total_items')->nullable();
            $table->integer('active_items')->nullable();
            $table->integer('inactive_items')->nullable();
            $table->integer('sold_items')->nullable();
            $table->longText('content')->nullable();
            $table->longText('filters')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
