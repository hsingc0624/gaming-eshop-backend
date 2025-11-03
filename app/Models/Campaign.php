<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $subject
 * @property string $html
 * @property string|null $segment
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property int|null $sent_count
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CampaignSend[] $sends
 */
class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','subject','html','segment','status','scheduled_at','sent_count'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function sends()
    {
        return $this->hasMany(CampaignSend::class);
    }
}
