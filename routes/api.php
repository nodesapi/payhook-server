<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MerchantApiController;
use App\Http\Controllers\Api\TupplyIntegrationController;
use Illuminate\Http\Request;

// Mobile App Authentication
Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login'])->name('api.mobile.login');
    Route::post('/verify', [MobileAuthController::class, 'verify'])->name('api.mobile.verify');
});

// Test Webhook Endpoint (for testing purposes)
Route::post('/test-webhook', function (Request $request) {
    \Illuminate\Support\Facades\Log::info('Test webhook received', [
        'payload' => $request->all(),
        'headers' => $request->headers->all(),
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Webhook received successfully',
        'received_at' => now()->toIso8601String(),
        'payload_size' => strlen(json_encode($request->all())),
    ], 200);
})->name('api.test-webhook');

Route::post('/payment-webhook', [WebhookController::class, 'handle'])
    ->name('api.webhook.handle');

Route::get('/pending-invoices', [WebhookController::class, 'pendingInvoices'])
    ->name('api.webhook.pending');

Route::post('/invoices/{invoice}/confirm', [WebhookController::class, 'confirm'])
    ->name('api.webhook.confirm');
// Internal API for Tupply Auto-Provisioning
Route::prefix('internal/tupply')
    ->middleware([\App\Http\Middleware\TupplyInternalAuth::class])
    ->group(function () {
        Route::post('/merchants', [TupplyIntegrationController::class, 'autoRegister'])
            ->name('api.internal.tupply.merchants.register');
        Route::post('/merchants/{tenant}/qris', [TupplyIntegrationController::class, 'uploadQris'])
            ->name('api.internal.tupply.merchants.qris');
    });

// Merchant API v1
Route::prefix('v1')->group(function () {
    Route::get('/channels', [MerchantApiController::class, 'getChannels'])
        ->name('api.v1.channels');

    Route::get('/webhook-config', [MerchantApiController::class, 'getWebhookConfig'])
        ->name('api.v1.webhook.config');

    Route::put('/webhook-config', [MerchantApiController::class, 'updateWebhookConfig'])
        ->name('api.v1.webhook.config.update');

    Route::get('/webhook-events', [MerchantApiController::class, 'listWebhookEvents'])
        ->name('api.v1.webhook.events');

    Route::post('/webhook-events/{transactionId}/retry', [MerchantApiController::class, 'retryWebhookEvent'])
        ->name('api.v1.webhook.events.retry');

    Route::post('/invoices', [MerchantApiController::class, 'createInvoice'])
        ->name('api.v1.invoices.create');

    Route::get('/invoices', [MerchantApiController::class, 'listInvoices'])
        ->name('api.v1.invoices.list');

    Route::get('/invoices/{invoice}', [MerchantApiController::class, 'getInvoice'])
        ->name('api.v1.invoices.show');

    Route::post('/invoices/{invoice}/cancel', [MerchantApiController::class, 'cancelInvoice'])
        ->name('api.v1.invoices.cancel');

    Route::post('/invoices/{invoice}/refund', [MerchantApiController::class, 'refundInvoice'])
        ->name('api.v1.invoices.refund');

    Route::get('/transactions', [MerchantApiController::class, 'listTransactions'])
        ->name('api.v1.transactions.list');

    Route::get('/transactions/{transactionId}', [MerchantApiController::class, 'getTransaction'])
        ->name('api.v1.transactions.show');
});
