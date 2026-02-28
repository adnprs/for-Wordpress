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
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // Enum hataya taaki 'custom' bhi aa sake
            $table->string('ai_key')->nullable(); // Name change kiya taaki Model se match kare
            $table->string('status')->default('active'); // Boolean hataya taaki 'active' text aa sake
            $table->timestamps(); // <--- YE MISSING THA (Isse error hat jayega)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};