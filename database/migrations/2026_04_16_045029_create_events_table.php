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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->index(); 
            $table->text('description');
            $table->string('location');
            $table->dateTime('event_date');            
            $table->integer('duration');
            $table->integer('quota');
            $table->enum('status', ['published', 'draft', 'archived'])->default('published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
