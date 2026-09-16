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
        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('stream_url');
            $table->unsignedInteger('max_width')->default(960);
            $table->unsignedInteger('quality')->default(5);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        // Carry over the single camera that used to be configured via .env
        // so it doesn't disappear from existing installs.
        if (! empty(env('ONVIF_STREAM_URL'))) {
            DB::table('cameras')->insert([
                'name' => 'Camera',
                'stream_url' => env('ONVIF_STREAM_URL'),
                'max_width' => (int) env('ONVIF_STREAM_MAX_WIDTH', 960),
                'quality' => (int) env('ONVIF_STREAM_QUALITY', 5),
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cameras');
    }
};
