<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'type','name','line1','line2','city','postcode','country'
    ];

    public function addressable()
    {
        return $this->morphTo();
    }
}
