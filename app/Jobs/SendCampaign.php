<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignSend;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Campaign $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        $c = $this->campaign->fresh();

        $users = User::query()
            ->when($c->segment === 'newsletter', fn($q) => $q->whereNotNull('email_verified_at'))
            ->get(['id','email']);

        foreach ($users as $u) {
            try {
                \Mail::html($c->html, function ($m) use ($u, $c) {
                    $m->to($u->email)->subject($c->subject);
                });

                CampaignSend::create([
                    'campaign_id' => $c->id,
                    'user_id'     => $u->id,
                    'email'       => $u->email,
                    'status'      => 'sent',
                    'provider_id' => null,
                ]);
            } catch (\Throwable $e) {
                CampaignSend::create([
                    'campaign_id' => $c->id,
                    'user_id'     => $u->id,
                    'email'       => $u->email,
                    'status'      => 'failed',
                    'provider_id' => null,
                ]);
            }
        }

        $c->update([
            'status'     => 'sent',
            'sent_count' => $users->count(),
        ]);
    }
}
