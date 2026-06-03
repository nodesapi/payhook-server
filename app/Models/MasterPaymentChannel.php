<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPaymentChannel extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'logo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path 
            ? asset('storage/' . $this->logo_path) 
            : null;
    }
}
