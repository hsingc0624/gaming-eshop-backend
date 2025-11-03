<?php

namespace App\Http\Controllers;

use App\Jobs\SendCampaign;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $r)
    {
        return response()->json(
            Campaign::query()->latest()->paginate($r->integer('per_page') ?: 20)
        );
    }

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

    public function sendTest(int $id, Request $r)
    {
        $data = $r->validate(['email' => ['required','email']]);

        $c = Campaign::findOrFail($id);

        \Mail::html($c->html, function ($m) use ($data, $c) {
            $m->to($data['email'])->subject('[Test] '.$c->subject);
        });

        return response()->noContent();
    }
}
