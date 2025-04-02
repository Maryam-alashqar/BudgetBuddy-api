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
       Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('category', ['need', 'want', 'primary_bill','tax'])->comment('Select expenses type');
            $table->string('expenses_name')->nullable()->comment('e.g. Flat rent, Electricity bill.. etc.');
            $table->double('expenses_amount')->default(0.00);
            $table->date('Deadline')->nullable();
            $table->double('expenses_total')->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
