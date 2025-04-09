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
        Schema::create('budget', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('needs_percentage')->default(30);
            $table->unsignedTinyInteger('wants_percentage')->default(20);
            $table->unsignedTinyInteger('savings_percentage')->default(50);
            $table->double('needs_amount')->nullable();
            $table->double('wants_amount')->nullable();
            $table->double('savings_amount')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget');
    }
};
