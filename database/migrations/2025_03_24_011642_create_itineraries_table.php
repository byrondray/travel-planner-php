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
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->date('date');
            $table->text('description')->nullable();
            $table->json('activities')->nullable(); // Store activities as JSON
            $table->json('transportation')->nullable(); // Store transportation details as JSON
            $table->json('accommodations')->nullable(); // Store accommodation details as JSON
            $table->json('meals')->nullable(); // Store meal plans as JSON
            $table->integer('position')->default(0); // Order of itineraries in the plan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itineraries');
    }
};