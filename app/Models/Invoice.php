<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'invoice_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'description',
        'amount',
        'unique_suffix',
        'unique_amount',
        'status',
        'paid_at',
        'payment_source',
        'payment_notification_text',
        'expires_at',
        'qris_template_id',
        'qris_string',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'unique_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }

            if (empty($invoice->unique_suffix)) {
                $invoice->unique_suffix = random_int(1, 999);
            }

            $invoice->unique_amount = $invoice->amount + $invoice->unique_suffix;

            if (empty($invoice->expires_at)) {
                $invoice->expires_at = now()->addDays(7);
            }
        });

        static::created(function ($invoice) {
            // Auto-generate QRIS after invoice is created
            $invoice->generateQris();
        });
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending')
                     ->where('expires_at', '>', now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function markAsPaid(string $source, ?string $notificationText = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_source' => $source,
            'payment_notification_text' => $notificationText,
        ]);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function paymentLogs()
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function qrisTemplate()
    {
        return $this->belongsTo(QrisTemplate::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function generateQris(?int $qrisTemplateId = null): void
    {
        $qrisService = app(\App\Services\QrisService::class);
        $qrisString = null;
        $templateIdToSave = null;

        // 1. Try to fetch the QRIS string from the Tenant's Payment Channel metadata
        $qrisChannel = \App\Models\PaymentChannel::where('tenant_id', $this->tenant_id)
            ->where('channel_type', 'qris')
            ->where('is_active', true)
            ->whereNotNull('metadata')
            ->latest()
            ->get()
            ->first(function ($channel) {
                return isset($channel->metadata['qris_string']);
            });

        if ($qrisChannel) {
            $qrisString = $qrisChannel->metadata['qris_string'];
        }

        // 2. Fallback to QrisTemplate if no channel has it
        if (!$qrisString) {
            $template = $qrisTemplateId 
                ? QrisTemplate::find($qrisTemplateId)
                : (QrisTemplate::active()->where('tenant_id', $this->tenant_id)->first() 
                   ?? QrisTemplate::active()->whereNull('tenant_id')->first());

            if ($template) {
                $qrisString = $template->qris_string;
                $templateIdToSave = $template->id;
            }
        }

        if (!$qrisString) {
            return;
        }

        // ✅ GENERATE DYNAMIC QRIS WITH AMOUNT EMBEDDED
        try {
            $dynamicQris = $qrisService->injectAmount($qrisString, $this->unique_amount);
            
            $this->update([
                'qris_template_id' => $templateIdToSave,
                'qris_string' => $dynamicQris,
            ]);
        } catch (\Exception $e) {
            // Fallback to static if injection fails
            $this->update([
                'qris_template_id' => $templateIdToSave,
                'qris_string' => $qrisString,
            ]);
        }
    }
}
