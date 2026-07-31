<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            // Accès Gmail
            $table->string('gmail_login_ofppt')->nullable();
            $table->string('gmail_ofppt_mdp')->nullable();

            // Accès OFPPT
            $table->string('ofppt_mdp')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('entreprise_clientes', function (Blueprint $table) {
            $table->dropColumn(['gmail_login_ofppt', 'gmail_ofppt_mdp', 'ofppt_mdp']);
        });
    }
};