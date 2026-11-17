<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained('medicines')->restrictOnDelete();
            $table->string('batch_number', 100);
            $table->date('expiration_date');
            $table->unsignedInteger('quantity_on_hand')->default(0);
            $table->decimal('cost_price', 12, 2);
            $table->date('received_date');
            $table->unique(['medicine_id', 'batch_number']);
            $table->index('expiration_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
