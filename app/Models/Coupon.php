<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $code
 * @property string $type
 * @property int $value
 * @property \Illuminate\Support\Carbon|null $starts_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property int|null $max_uses
 * @property int|null $uses
 */
class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','type','value','starts_at','ends_at','max_uses','uses'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'value'     => 'integer',
        'max_uses'  => 'integer',
        'uses'      => 'integer',
    ];

    public function isActive(): bool
    {
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;
        if ($this->max_uses && $this->uses >= $this->max_uses) return false;
        return true;
    }
}
