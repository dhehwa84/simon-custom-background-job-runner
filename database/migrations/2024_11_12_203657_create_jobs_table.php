<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('background_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('class_name');
            $table->string('method');
            $table->text('parameters')->nullable();
            $table->integer('retries')->default(0);
            $table->integer('retry_count')->default(0);
            $table->integer('priority')->default(0);
            $table->string('status')->default('pending'); // pending, running, failed, completed
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_jobs');
    }
};
