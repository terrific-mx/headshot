<?php

use App\Models\User;
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
        Schema::create('headshots', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->index();
            $table->string('name');
            $table->string('age')->nullable();
            $table->string('ethnicity')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('body_type')->nullable();
            $table->string('eye_color')->nullable();
            $table->string('gender')->nullable();
            $table->string('glasses')->nullable();
            $table->string('status')->default('pending');
            $table->string('training_status')->default('pending');
            $table->string('trigger_word');
            $table->string('train_request_id')->nullable()->index();
            $table->string('lora_file_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('headshots');
    }
};
