<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sensors', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('reachable');
        });

        DB::table('sensors')->orderBy('id')->select('id')->get()->each(function ($sensor, $index) {
            DB::table('sensors')->where('id', $sensor->id)->update(['order' => $index]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensors', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
