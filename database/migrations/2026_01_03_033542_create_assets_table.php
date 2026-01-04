<?php
// database/migrations/xxxx_xx_xx_create_assets_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('symbol', 10); // BTC, ETH, etc.
            $table->decimal('amount', 20, 8)->default(0);
            $table->decimal('locked_amount', 20, 8)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'symbol']);
            $table->index(['symbol', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
