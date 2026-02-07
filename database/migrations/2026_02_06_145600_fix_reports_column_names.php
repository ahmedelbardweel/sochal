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
        Schema::table('reports', function (Blueprint $table) {
            $table->renameColumn('reported_type', 'reportable_type');
            $table->renameColumn('reported_id', 'reportable_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->renameColumn('reportable_type', 'reported_type');
            $table->renameColumn('reportable_id', 'reported_id');
        });
    }
};
