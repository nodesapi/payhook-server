<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentChannel extends Model
{
    protected $fillable = [
        'tenant_id',
        'channel_type',
        'channel_name',
        'provider',
        'account_number',
        'account_name',
        'merchant_id',
        'qr_code_path',
        'is_active',
        'fee_percentage',
        'fee_fixed',
        'description',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fee_percentage' => 'decimal:2',
        'fee_fixed' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function masterChannel(): BelongsTo
    {
        return $this->belongsTo(MasterPaymentChannel::class, 'provider', 'code');
    }

    public function getChannelTypeNameAttribute(): string
    {
        return match($this->channel_type) {
            'qris' => 'QRIS',
            'gopay' => 'GoPay',
            'dana' => 'DANA',
            'ovo' => 'OVO',
            'linkaja' => 'LinkAja',
            'shopeepay' => 'ShopeePay',
            'bank_transfer' => 'Bank Transfer',
            'virtual_account' => 'Virtual Account',
            default => ucfirst($this->channel_type)
        };
    }

    public function getQrCodeUrlAttribute(): ?string
    {
        return $this->qr_code_path 
            ? asset('storage/' . $this->qr_code_path) 
            : null;
    }
}
