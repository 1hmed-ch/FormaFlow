<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            if (! Schema::hasColumn('formations', 'date_debut')) {
                $table->date('date_debut')->nullable()->after('intitule');
            }
            if (! Schema::hasColumn('formations', 'date_fin')) {
                $table->date('date_fin')->nullable()->after('date_debut');
            }
        });
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn(['date_debut', 'date_fin']);
        });
    }
};
