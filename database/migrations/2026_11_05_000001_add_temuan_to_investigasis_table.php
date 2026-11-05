<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('investigasis', 'temuan')) {
            Schema::table('investigasis', function (Blueprint $table) {
                $table->unsignedTinyInteger('temuan')->default(0)->after('status_sent_client');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('investigasis', 'temuan')) {
            Schema::table('investigasis', function (Blueprint $table) {
                $table->dropColumn('temuan');
            });
        }
    }
};
