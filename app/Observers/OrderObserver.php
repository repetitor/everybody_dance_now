<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Вызывается перед обновлением модели
     */
    public function updating(Order $order): void
    {
        // Получаем оригинальное значение статуса до изменения
        $originalStatus = $order->getOriginal('status');

        // Если статус изменился
        if ($order->isDirty('status')) {
            // Можно выполнить какие-то проверки или подготовительные действия
            Log::info("Order #{$order->id} status changing from {$originalStatus} to {$order->status}");
            Log::info("getAttributeValue - Order #{$order->id} status changing from {$originalStatus} to {$order->getAttributeValue('status')}");
            Log::info("getOriginal - Order #{$order->id} status changing from {$originalStatus} to {$order->getOriginal('status')}");
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $originalStatus = $order->getOriginal('status');

        // Если статус изменился
        if ($order->wasChanged('status')) {
//            // Отправляем уведомление (синхронно)
//            $order->user->notify(new OrderStatusUpdated($order));
//
//            // Или через очередь (если QUEUE_CONNECTION не sync)
//            ProcessOrderStatusChange::dispatch($order, $originalStatus);

            Log::info("Order #{$order->id} status changed from {$originalStatus} to {$order->status}");
            Log::info("getAttributeValue - Order #{$order->id} status changed from {$originalStatus} to {$order->getAttributeValue('status')}");
            Log::info("getOriginal - Order #{$order->id} status changed from {$originalStatus} to {$order->getOriginal('status')}");
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
