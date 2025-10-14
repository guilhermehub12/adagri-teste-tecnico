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
        Schema::create('unidades_producao', function (Blueprint $table) {
            $table->id();
            $table->string('nome_cultura');
            $table->decimal('area_total_ha', 10, 2);
            $table->text('coordenadas_geograficas')->nullable();
            $table->foreignId('propriedade_id')->constrained('propriedades')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades_producao');
    }
};
