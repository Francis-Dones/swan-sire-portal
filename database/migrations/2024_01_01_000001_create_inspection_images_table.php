<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inspection_images', function (Blueprint $table) {
            $table->id();
            $table->integer('vessel_id')->nullable()->index();
            $table->integer('inspection_id')->nullable()->index();
            $table->string('image_name');
            $table->text('image_data')->nullable();
            $table->string('image_mime_type')->nullable();
            $table->string('inspection_type')->nullable()->index();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('inspection_images'); }
};
