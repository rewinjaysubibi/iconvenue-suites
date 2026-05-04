@extends('layouts.admin')

@section('page-title', 'Contact Settings')

@section('main-content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $settings->phone) }}" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $settings->email) }}" 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">WhatsApp Number</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $settings->whatsapp) }}" 
                    placeholder="+1234567890"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Facebook Page URL</label>
                <input type="url" name="facebook" value="{{ old('facebook', $settings->facebook) }}" 
                    placeholder="https://facebook.com/yourpage"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Facebook Page URL</label>
                <input type="url" name="facebook" value="{{ old('facebook', $settings->facebook) }}" 
                    placeholder="https://facebook.com/yourpage"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Business Hours</label>
                <input type="text" name="business_hours" value="{{ old('business_hours', $settings->business_hours) }}" 
                    placeholder="Monday - Sunday: 9:00 AM - 6:00 PM"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-700 font-semibold mb-2">Google Form URL (Contact Form)</label>
                <input type="url" name="google_form_url" value="{{ old('google_form_url', $settings->google_form_url) }}" 
                    placeholder="https://forms.gle/yourformid"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                <p class="text-sm text-gray-500 mt-1">When clients click the email link, they'll be directed to this Google Form instead of opening their email client</p>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-gray-700 font-semibold mb-2">Address</label>
            <textarea name="address" rows="3" 
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">{{ old('address', $settings->address) }}</textarea>
        </div>

        <div class="mt-8">
            <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
                Update Settings
            </button>
        </div>
    </form>
</div>
@endsection
