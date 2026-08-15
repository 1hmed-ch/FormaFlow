<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gerants', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->required();
            $table->string('prenom')->required();
            $table->string('fonction')->nullable();
            $table->string('cin')->required()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gerants');
    }
};