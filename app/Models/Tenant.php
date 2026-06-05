<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plan_id',
        'name',
        'slug',
        'email',
        'phone',
        'api_key_production',
        'api_key_sandbox',
        'webhook_url',
        'webhook_secret',
        'webhook_enabled',
        'callback_url',
        'mode',
        'is_active',
        'description',
        'logo_url',
        'website',
        'monthly_limit',
        'settings',
        'activated_at',
        'suspended_at',
        'expired_at',
        'kyc_status',
        'ktp_name',
        'ktp_number',
        'ktp_image_path',
        'kyc_reject_reason',
    ];

    protected $casts = [
        'plan_id' => 'integer',
        'webhook_enabled' => 'boolean',
        'is_active' => 'boolean',
        'monthly_limit' => 'decimal:2',
        'settings' => 'array',
        'activated_at' => 'datetime',
        'suspended_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    protected $hidden = [
        'api_key_production',
        'api_key_sandbox',
        'webhook_secret',
    ];

    // Boot method untuk auto-generate slug dan API keys
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name);
            }

            if (empty($tenant->api_key_sandbox)) {
                $tenant->api_key_sandbox = 'sk_test_' . Str::random(40);
            }

            if (empty($tenant->api_key_production)) {
                $tenant->api_key_production = 'sk_live_' . Str::random(40);
            }

            if (empty($tenant->webhook_secret)) {
                $tenant->webhook_secret = Str::random(32);
            }
        });
    }

    // Relationships
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function qrisTemplates(): HasMany
    {
        return $this->hasMany(QrisTemplate::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function paymentLogs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function paymentChannels(): HasMany
    {
        return $this->hasMany(PaymentChannel::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeProduction($query)
    {
        return $query->where('mode', 'production');
    }

    public function scopeSandbox($query)
    {
        return $query->where('mode', 'sandbox');
    }

    // Helper Methods
    public function generateProductionKey(): string
    {
        $this->api_key_production = 'sk_live_' . Str::random(40);
        $this->save();
        return $this->api_key_production;
    }

    public function regenerateSandboxKey(): string
    {
        $this->api_key_sandbox = 'sk_test_' . Str::random(40);
        $this->save();
        return $this->api_key_sandbox;
    }

    public function activate(): void
    {
        $this->is_active = true;
        $this->activated_at = now();
        $this->suspended_at = null;
        $this->save();
    }

    public function suspend(): void
    {
        $this->is_active = false;
        $this->suspended_at = now();
        $this->save();
    }

    public function switchToProduction(): void
    {
        if (empty($this->api_key_production)) {
            $this->generateProductionKey();
        }
        $this->mode = 'production';
        $this->save();
    }

    public function switchToSandbox(): void
    {
        $this->mode = 'sandbox';
        $this->save();
    }

    // Get active API key based on mode
    public function getActiveApiKey(): ?string
    {
        return $this->mode === 'production'
            ? $this->api_key_production
            : $this->api_key_sandbox;
    }

    public function isSubscriptionActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->expired_at) {
            return true; // No expiry set, assume permanent for now or handle accordingly
        }

        return $this->expired_at->isFuture();
    }

    public function getSubscriptionDaysLeft(): int
    {
        if (!$this->expired_at) {
            return 0;
        }

        return max(0, (int) now()->diffInDays($this->expired_at, false));
    }
}

