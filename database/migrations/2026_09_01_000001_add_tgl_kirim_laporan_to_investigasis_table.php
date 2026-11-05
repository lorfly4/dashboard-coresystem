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
        if (!Schema::hasColumn('investigasis', 'tgl_kirim_laporan')) {
            Schema::table('investigasis', function (Blueprint $table) {
                $table->date('tgl_kirim_laporan')->nullable()->after('tgl_registrasi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('investigasis', 'tgl_kirim_laporan')) {
            Schema::table('investigasis', function (Blueprint $table) {
                $table->dropColumn('tgl_kirim_laporan');
            });
        }
    }
};
