<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tb_exam', function (Blueprint $table) {
            $table->id();
            $table->string('exam_id')->nullable()->index();
            $table->string('vessel_name')->nullable()->index();
            $table->string('person_in_charge')->nullable();
            $table->timestamp('submitted_date')->nullable();
            $table->string('submitted_by')->nullable();
            $table->string('email')->nullable();
            $table->json('answers')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tb_exam'); }
};
