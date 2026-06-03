<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $fillable = [
        'tenant_id',
        'payment_channel_id',
        'transaction_id',
        'external_id',
        'amount',
        'fee',
        'net_amount',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_method',
        'payment_reference',
        'paid_at',
        'expired_at',
        'webhook_sent',
        'webhook_sent_at',
        'webhook_attempts',
        'webhook_response',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'webhook_sent' => 'boolean',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'webhook_sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (!$transaction->transaction_id) {
                $transaction->transaction_id = 'PHK-' . strtoupper(Str::random(12));
            }
            
            if (!$transaction->expired_at && $transaction->status === 'pending') {
                $transaction->expired_at = now()->addHours(24);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function paymentChannel(): BelongsTo
    {
        return $this->belongsTo(PaymentChannel::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'success' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Success</span>',
            'pending' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Pending</span>',
            'processing' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Processing</span>',
            'failed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-700">Failed</span>',
            'expired' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700">Expired</span>',
            'refund' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">Refund</span>',
            default => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">' . ucfirst($this->status) . '</span>'
        };
    }
}
