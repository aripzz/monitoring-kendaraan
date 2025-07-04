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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->string('model');
            $table->enum('type', ['pengangkut_orang', 'pengangkut_barang']);
            $table->enum('owner', ['inhouse', 'rental']);
            $table->decimal('bbm', 8, 2)->nullable(); // Konsumsi BBM (km/L)
            $table->date('next_service_date')->nullable(); // Jadwal service
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
