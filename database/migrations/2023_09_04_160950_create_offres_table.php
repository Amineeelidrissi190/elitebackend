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
        Schema::create('offres', function (Blueprint $table) {
            $table->id();
            $table->string("nom_offre");
            $table->string("date_offre_deb");
            $table->string("date_offre_fin");
            $table->text("content_offre");
            $table->timestamps();
            $table->unsignedBigInteger('specialite_id');
            $table->foreign('specialite_id')->references('id')->on('specialites');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offres');
    }
};
