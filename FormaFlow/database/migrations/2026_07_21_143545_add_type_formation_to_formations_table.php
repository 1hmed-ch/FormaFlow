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
    Schema::table('formations', function (Blueprint $table) {
        $table->string('type_formation')->nullable()->default('les_deux')->after('statut');
    });
}

public function down(): void
{
    Schema::table('formations', function (Blueprint $table) {
        $table->dropColumn('type_formation');
    });
}
};
