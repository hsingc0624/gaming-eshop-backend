<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /** @param Request $r @return \Illuminate\Http\JsonResponse */
    public function store(Request $r)
    {
        $data = $r->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $msg = ContactMessage::create($data);

        return response()->json(['ok' => true, 'id' => $msg->id], 201);
    }
}
