<?php

namespace App\Http\Controllers;

use App\Jobs\SendCampaign;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CampaignController extends Controller
{
    /**
     * @param  Request  $r
     * @return JsonResponse
     */
    public function index(Request $r)
    {
        return response()->json(
            Campaign::query()->latest()->paginate($r->integer('per_page') ?: 20)
        );
    }

    /**
     * @param  Request  $r
     * @return JsonResponse
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'name'    => ['required','string','max:200'],
            'subject' => ['required','string','max:200'],
            'html'    => ['required','string'],
            'segment' => ['nullable','string','max:100'],
        ]);

        $c = Campaign::create($data);
        return response()->json($c, 201);
    }

    /**
     * @param  int      $id
     * @param  Request  $r
     * @return JsonResponse
     */
    public function schedule(int $id, Request $r)
    {
        $data = $r->validate([
            'scheduled_at' => ['required','date'],
        ]);

        $c = Campaign::findOrFail($id);
        $c->update(['status' => 'scheduled', 'scheduled_at' => $data['scheduled_at']]);

        SendCampaign::dispatch($c)->delay($c->scheduled_at);

        return response()->json($c->fresh());
    }

    /**
     * @param  int      $id
     * @param  Request  $r
     * @return Response
     */
    public function sendTest(int $id, Request $r)
    {
        $data = $r->validate(['email' => ['required','email']]);

        $c = Campaign::findOrFail($id);

        \Mail::html($c->html, function ($m) use ($data, $c) {
            $m->to($data['email'])->subject('[Test] '.$c->subject);
        });

        return response()->noContent();
    }

    /**
     * @param  Request  $r
     * @return JsonResponse
     */
    public function metrics(Request $r)
    {
        $days = (int)($r->integer('days') ?: 30);

        $rows = \DB::table('campaigns')
            ->selectRaw('DATE(created_at) as d, COALESCE(SUM(sent_count),0) as s')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $byDate = $rows->keyBy('d');
        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $out[] = [
                'date' => $day,
                'sent' => (int)($byDate[$day]->s ?? 0),
            ];
        }

        return response()->json($out);
    }
}
