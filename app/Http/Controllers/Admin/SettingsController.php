<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = ContactSetting::first() ?? new ContactSetting();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'facebook' => 'nullable|url',
            'messenger' => 'nullable|url',
            'whatsapp' => 'nullable|string',
            'address' => 'nullable|string',
            'business_hours' => 'nullable|string',
            'google_form_url' => 'nullable|url'
        ]);

        $settings = ContactSetting::first();

        if ($settings) {
            $settings->update($validated);
        } else {
            ContactSetting::create($validated);
        }

        return back()->with('success', 'Settings updated successfully!');
    }
}
