<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->foreignId('consultation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->nullable()->unique();
            $table->json('details')->nullable();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('reference')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('reference');
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropConstrainedForeignId('consultation_id');
            $table->dropColumn(['reference', 'details']);
        });
    }
};
