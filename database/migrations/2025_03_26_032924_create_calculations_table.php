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
        // id, 계산식 expressions, 결과값 result, 수식을 잘못넣었을 때 is_vaild
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->string('expressions');
            $table->double('result');
            $table->boolean('is_valid')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};
