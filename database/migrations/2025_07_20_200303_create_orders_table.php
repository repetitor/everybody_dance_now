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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Связь с пользователем
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Информация о заказе
            $table->string('number')->unique(); // Уникальный номер заказа
            $table->decimal('total_amount', 10, 2); // Общая сумма
            $table->decimal('tax_amount', 10, 2)->default(0); // Налог
            $table->decimal('shipping_amount', 10, 2)->default(0); // Стоимость доставки

            $table->enum('status', [
                'pending',
                'processing',
                'shipped',
                'completed',
                'cancelled',
                'refunded'
            ])->default('pending');

            // Информация о платеже
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable()->default('unpaid'); // unpaid, paid, failed, refunded

            // Адрес доставки (можно вынести в отдельную таблицу для сложных случаев)
            $table->json('shipping_address')->nullable();
            $table->json('billing_address')->nullable();

            // Даты важных событий
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Комментарии
            $table->text('notes')->nullable();

            // Стандартные timestamps
            $table->timestamps();
            $table->softDeletes(); // Мягкое удаление
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
