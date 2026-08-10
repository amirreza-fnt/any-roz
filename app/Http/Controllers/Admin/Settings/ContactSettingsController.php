<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\Request;

class ContactSettingsController extends Controller
{
    public function edit()
    {
        $settings = ContactSetting::singleton();
        $socialOptions = ContactSetting::socialNetworkOptions();

        return view('backend.settings.EditContactSettings', compact('settings', 'socialOptions'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'working_hours' => 'nullable|string|max:8000',
            'fax' => 'nullable|string|max:64',
            'support_title' => 'nullable|string|max:255',
            'footer_note' => 'nullable|string|max:8000',
        ]);

        $phones = array_values(array_filter($request->input('phones', []), fn ($v) => trim((string) $v) !== ''));
        $emails = [];
        foreach ($request->input('emails', []) as $e) {
            $e = trim((string) $e);
            if ($e === '') {
                continue;
            }
            if (filter_var($e, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $e;
            }
        }
        $emails = array_values(array_unique($emails));

        $addresses = [];
        foreach ($request->input('addresses', []) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $body = trim((string) ($row['body'] ?? ''));
            if ($body === '' && trim((string) ($row['label'] ?? '')) === '') {
                continue;
            }
            $addresses[] = [
                'label' => trim((string) ($row['label'] ?? '')),
                'body' => $body,
                'map_url' => trim((string) ($row['map_url'] ?? '')),
            ];
        }

        $socials = [];
        $allowed = array_keys(ContactSetting::socialNetworkOptions());
        foreach ($request->input('socials', []) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $net = $row['network'] ?? '';
            $url = trim((string) ($row['url'] ?? ''));
            if ($url === '' || ! in_array($net, $allowed, true)) {
                continue;
            }
            $socials[] = ['network' => $net, 'url' => $url];
        }

        $settings = ContactSetting::singleton();
        $settings->update([
            'phones' => $phones,
            'emails' => $emails,
            'addresses' => $addresses,
            'socials' => $socials,
            'working_hours' => $request->input('working_hours'),
            'fax' => $request->input('fax'),
            'support_title' => $request->input('support_title'),
            'footer_note' => $request->input('footer_note'),
        ]);

        message('success', 'اطلاعات ارتباطی ذخیره شد.');

        return redirect()->route('admin.contact-settings.edit');
    }
}
