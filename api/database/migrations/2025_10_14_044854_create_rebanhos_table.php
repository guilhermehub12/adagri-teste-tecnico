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
        Schema::create('rebanhos', function (Blueprint $table) {
            $table->id();
            $table->string('especie');
            $table->integer('quantidade');
            $table->string('finalidade')->nullable();
            $table->timestamp('data_atualizacao')->nullable();
            $table->foreignId('propriedade_id')->constrained('propriedades')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rebanhos');
    }
};
