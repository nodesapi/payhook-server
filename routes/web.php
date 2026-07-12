<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\WebhookLogController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\PaymentChannelController;
use App\Http\Controllers\Tenant\TransactionController;
use App\Http\Controllers\Tenant\ApiPlaygroundController;
use App\Http\Controllers\Tenant\SettingsController;
use Illuminate\Support\Facades\Route;

// Public Localized Routes Registration Helper
if (!function_exists('registerPublicRoutes')) {
    function registerPublicRoutes($prefix = null) {
        $groupPrefix = $prefix ? $prefix : '';
        $namePrefix = $prefix ? 'en.' : '';
        
        Route::group(['prefix' => $groupPrefix, 'as' => $namePrefix], function () use ($prefix) {
            Route::get('/', function () use ($prefix) {
                app()->setLocale($prefix ?: 'id');
                return view('welcome');
            })->name('home');

            Route::get('/docs', function () use ($prefix) {
                app()->setLocale($prefix ?: 'id');
                return view('docs.index');
            })->name('public.docs');

            Route::get('/pricing', function () use ($prefix) {
                app()->setLocale($prefix ?: 'id');
                $plans = \App\Models\Plan::where('is_active', true)->get();
                return view('pricing', compact('plans'));
            })->name('public.pricing');

            Route::get('/terms', function () use ($prefix) {
                app()->setLocale($prefix ?: 'id');
                return view('terms');
            })->name('public.terms');

            Route::get('/privacy', function () use ($prefix) {
                app()->setLocale($prefix ?: 'id');
                return view('privacy');
            })->name('public.privacy');

            // Public Invoice Routes
            Route::get('/invoices/{invoice:invoice_number}', [\App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
            Route::get('/invoices/{invoice:invoice_number}/pdf', [\App\Http\Controllers\InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
            Route::get('/invoices/{invoice:invoice_number}/print', [\App\Http\Controllers\InvoiceController::class, 'print'])->name('invoices.print');
        });
    }
}

registerPublicRoutes();
registerPublicRoutes('en');

// Dashboard router - redirect based on user role
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    $user = auth()->user();
    if ($user->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    
    return redirect()->route('tenant.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Dashboard Routes (Protected)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Tenant Management
    Route::resource('tenants', TenantController::class);
    Route::post('tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
    Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::post('tenants/{tenant}/regenerate-key', [TenantController::class, 'regenerateKey'])->name('tenants.regenerate-key');
    Route::post('tenants/{tenant}/extend', [TenantController::class, 'extend'])->name('tenants.extend');
    Route::post('tenants/{tenant}/approve-kyc', [TenantController::class, 'approveKyc'])->name('tenants.approve-kyc');
    Route::post('tenants/{tenant}/reject-kyc', [TenantController::class, 'rejectKyc'])->name('tenants.reject-kyc');
    Route::post('tenants/{tenant}/approve-upgrade', [TenantController::class, 'approveUpgrade'])->name('tenants.approve-upgrade');
    Route::post('tenants/{tenant}/reject-upgrade', [TenantController::class, 'rejectUpgrade'])->name('tenants.reject-upgrade');
    
    // Webhook Logs
    Route::get('webhook-logs', [WebhookLogController::class, 'index'])->name('webhook-logs.index');
    Route::get('webhook-logs/{log}', [WebhookLogController::class, 'show'])->name('webhook-logs.show');

    // Subscription Plans
    Route::resource('plans', PlanController::class);

    // Master Payment Channels
    Route::resource('master-channels', \App\Http\Controllers\Admin\MasterPaymentChannelController::class);

    // System Config & Platform Billing
    Route::get('system-config', [\App\Http\Controllers\Admin\SystemConfigController::class, 'index'])->name('system-config.index');
    Route::post('system-config/initialize', [\App\Http\Controllers\Admin\SystemConfigController::class, 'initializeBilling'])->name('system-config.initialize');
    Route::post('system-config/smtp', [\App\Http\Controllers\Admin\SystemConfigController::class, 'updateSmtp'])->name('system-config.smtp');
    Route::post('system-config/qris', [\App\Http\Controllers\Admin\SystemConfigController::class, 'storeQris'])->name('system-config.qris');
    Route::delete('system-config/qris/{id}', [\App\Http\Controllers\Admin\SystemConfigController::class, 'deleteQris'])->name('system-config.qris.destroy');
    Route::post('/system-config/bank', [\App\Http\Controllers\Admin\SystemConfigController::class, 'storeBank'])->name('system-config.bank');
    Route::delete('/system-config/bank/{id}', [\App\Http\Controllers\Admin\SystemConfigController::class, 'deleteBank'])->name('system-config.bank.delete');
    Route::post('/system-config/ewallet', [\App\Http\Controllers\Admin\SystemConfigController::class, 'storeEwallet'])->name('system-config.ewallet');
});

// Tenant Setup Routes (Auth only, no KYC/Subscription check)
Route::middleware(['auth', 'verified'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/setup', [\App\Http\Controllers\Tenant\SetupController::class, 'create'])->name('setup');
    Route::post('/setup', [\App\Http\Controllers\Tenant\SetupController::class, 'store'])->name('setup.store');
    
    Route::get('/kyc-pending', [\App\Http\Controllers\Tenant\SetupController::class, 'pending'])->name('kyc.pending');
    Route::post('/kyc-pending/payment-proof', [\App\Http\Controllers\Tenant\SetupController::class, 'uploadPaymentProof'])->name('kyc.upload-payment');

    Route::get('/kyc-rejected', function () {
        return view('tenant.kyc-rejected');
    })->name('kyc.rejected');
});

// Tenant Dashboard Routes (Protected)
Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckKyc::class])->prefix('tenant')->name('tenant.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/live-stats', [TenantDashboardController::class, 'liveStats'])->name('dashboard.live-stats');
    
    // Payment Channels
    Route::resource('payment-channels', PaymentChannelController::class);
    Route::post('payment-channels/{paymentChannel}/toggle', [PaymentChannelController::class, 'toggle'])->name('payment-channels.toggle');
    
    // Transactions
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
    Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('transactions/{transaction}/refund', [TransactionController::class, 'refund'])->name('transactions.refund');
    Route::post('transactions/{transaction}/resend-webhook', [TransactionController::class, 'resendWebhook'])->name('transactions.resend-webhook');
    Route::post('transactions/{transaction}/confirm', [TransactionController::class, 'confirm'])->name('transactions.confirm');
    
    // API Playground
    Route::get('api-playground', [ApiPlaygroundController::class, 'index'])->name('api-playground');
    Route::post('api-playground/test-webhook', [ApiPlaygroundController::class, 'testWebhook'])->name('api-playground.test-webhook');
    Route::post('api-playground/create-transaction', [ApiPlaygroundController::class, 'createTransaction'])->name('api-playground.create-transaction');
    Route::get('api-playground/invoice-status/{invoice}', [ApiPlaygroundController::class, 'invoiceStatus'])->name('api-playground.invoice-status');
    
    // Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/request-upgrade', [SettingsController::class, 'requestUpgrade'])->name('settings.request-upgrade');
    Route::post('settings/regenerate-api-key', [SettingsController::class, 'regenerateApiKey'])->name('settings.regenerate-api-key');
    Route::post('settings/regenerate-webhook-secret', [SettingsController::class, 'regenerateWebhookSecret'])->name('settings.regenerate-webhook-secret');
    Route::post('settings/2fa/generate', [App\Http\Controllers\Auth\TwoFactorController::class, 'generate'])->name('settings.2fa.generate');
    Route::post('settings/2fa/confirm', [App\Http\Controllers\Auth\TwoFactorController::class, 'confirm'])->name('settings.2fa.confirm');
    Route::post('settings/2fa/disable', [App\Http\Controllers\Auth\TwoFactorController::class, 'disable'])->name('settings.2fa.disable');
    
    // Documentation
    Route::get('documentation', function () {
        return view('tenant.documentation');
    })->name('documentation');
    
    // Legacy routes (keep for compatibility if needed)
    Route::get('/download-apk', [TenantDashboardController::class, 'downloadApk'])->name('download-apk');
    Route::post('/test-webhook', [TenantDashboardController::class, 'testWebhook'])->name('test-webhook');
    Route::post('/test-payment', [TenantDashboardController::class, 'testPayment'])->name('test-payment');
    Route::get('/setup-guide', [TenantDashboardController::class, 'setupGuide'])->name('setup-guide');

    Route::get('subscription-expired', function () {
        $user = auth()->user();
        $tenant = \App\Models\Tenant::where('email', $user->email)->first();
        return view('tenant.subscription-expired', compact('tenant'));
    })->name('subscription-expired');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('auth')->group(function () {
    require __DIR__.'/auth.php';
});

Route::get('auth/two-factor', [App\Http\Controllers\Auth\TwoFactorController::class, 'showChallenge'])->name('login.two-factor');
Route::post('auth/two-factor', [App\Http\Controllers\Auth\TwoFactorController::class, 'verifyChallenge']);
