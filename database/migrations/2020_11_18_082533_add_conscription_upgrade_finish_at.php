<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConscriptionUpgradeFinishAt extends Migration
{
    public function up(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->timestamp('conscription_upgrade_finished_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn('conscription_upgrade_finished_at');
        });
    }
}
