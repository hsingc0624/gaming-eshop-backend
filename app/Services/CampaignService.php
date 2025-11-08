<?php

namespace App\Services;

use App\Jobs\SendCampaign;
use App\Models\Campaign;
use App\Repositories\CampaignRepositoryInterface;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class CampaignService
{
    /**
     * @param CampaignRepositoryInterface $campaigns
     */
    public function __construct(
        private CampaignRepositoryInterface $campaigns
    ) {}

    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function index(int $perPage): LengthAwarePaginator
    {
        return $this->campaigns->paginate($perPage);
    }

    /**
     * @param array $data
     * @return Campaign
     */
    public function store(array $data): Campaign
    {
        return $this->campaigns->create($data);
    }

    /**
     * @param int    $id
     * @param string $scheduledAt
     * @return Campaign
     */
    public function schedule(int $id, string $scheduledAt): Campaign
    {
        $c = $this->campaigns->find($id);

        $c = $this->campaigns->update($c, [
            'status'       => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);

        SendCampaign::dispatch($c)->delay(
            $c->scheduled_at instanceof CarbonInterface ? $c->scheduled_at : now()
        );

        return $c;
    }

    /**
     * @param int    $id
     * @param string $email
     * @return void
     */
    public function sendTest(int $id, string $email): void
    {
        $c = $this->campaigns->find($id);

        Mail::html($c->html, function ($m) use ($email, $c) {
            $m->to($email)
              ->subject('[Test] ' . $c->subject);
        });
    }

    /**
     * @param int $days
     * @return array<int, array{date:string, sent:int}>
     */
    public function metrics(int $days): array
    {
        $rows   = $this->campaigns->metricsRows($days);
        $byDate = $rows->keyBy('d');

        $out = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();

            $out[] = [
                'date' => $day,
                'sent' => (int) ($byDate[$day]->s ?? 0),
            ];
        }

        return $out;
    }
}
