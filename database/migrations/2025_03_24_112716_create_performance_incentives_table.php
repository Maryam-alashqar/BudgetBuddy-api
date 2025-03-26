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
          Schema::create('performance_incentives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_user_id')->constrained('fixed_users')->onDelete('cascade');
            $table->double('performance_amount')->nullable();
            $table->date('received_date')->nullable();
            $table->boolean('is_permanent')->default('0')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_incentives');
    }
};
