<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrisTemplate extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'qris_string',
        'account_name',
        'account_number',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
