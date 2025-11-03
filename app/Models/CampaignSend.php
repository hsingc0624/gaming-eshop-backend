<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $campaign_id
 * @property int|null $user_id
 * @property string $email
 * @property string|null $status
 * @property string|null $provider_id
 *
 * @property-read \App\Models\Campaign $campaign
 * @property-read \App\Models\User|null $user
 */
class CampaignSend extends Model
{
    use HasFactory;

    protected $fillable = ['campaign_id','user_id','email','status','provider_id'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
