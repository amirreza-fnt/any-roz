<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function index(): JsonResponse
    {
        $contact = ContactSetting::singleton();

        return response()->json([
            'data' => [
                'phones' => $contact->phones ?? [],
                'emails' => $contact->emails ?? [],
                'addresses' => $contact->addresses ?? [],
                'socials' => $contact->socials ?? [],
                'working_hours' => $contact->working_hours,
                'fax' => $contact->fax,
                'support_title' => $contact->support_title,
                'footer_note' => $contact->footer_note,
            ],
        ]);
    }
}
