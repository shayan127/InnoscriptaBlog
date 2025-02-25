<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title',500);
            $table->text('snippet');
            $table->text('content');
            $table->timestamp('published_at')->nullable();
            $table->string('source')->nullable();
            $table->json('authors')->nullable();
            $table->string('image',500)->nullable();
            $table->string('url',500)->nullable();
            $table->string('origin')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
