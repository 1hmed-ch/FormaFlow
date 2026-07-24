<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entreprise_formations', function (Blueprint $table) {
            $table->string('ville')->nullable()->after('siege_social');
     
        });
    }

    public function down(): void
    {
        Schema::table('entreprise_formations', function (Blueprint $table) {
            $table->dropColumn('ville');
        });
    }
};