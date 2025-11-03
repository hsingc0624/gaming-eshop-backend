<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = ['name','subject','html','segment','status','scheduled_at'];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function sends()
    {
        return $this->hasMany(CampaignSend::class);
    }
}
