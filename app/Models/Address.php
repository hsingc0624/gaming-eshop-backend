<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $type
 * @property string $name
 * @property string $line1
 * @property string|null $line2
 * @property string $city
 * @property string $postcode
 * @property string $country
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $addressable
 */
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
