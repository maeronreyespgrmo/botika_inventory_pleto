<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->restrictOnDelete();
            $table->string('generic_name', 150);
            $table->string('brand_name', 150)->nullable();
            $table->string('dosage_strength', 50)->nullable();
            $table->string('dosage_form', 50)->nullable();
            $table->string('barcode', 100)->nullable()->unique();
            $table->string('unit', 30);
            $table->decimal('selling_price', 12, 2);
            $table->unsignedInteger('reorder_level')->default(10);
            $table->boolean('requires_prescription')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
