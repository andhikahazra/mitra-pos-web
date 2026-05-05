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
        Schema::table('rop', function (Blueprint $table) {
            $table->renameColumn('updated_at', 'waktu_penghitungan');
        });
    }

    public function down(): void
    {
        Schema::table('rop', function (Blueprint $table) {
            $table->renameColumn('waktu_penghitungan', 'updated_at');
        });
    }
};
