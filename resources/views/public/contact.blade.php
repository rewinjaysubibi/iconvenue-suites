@extends('layouts.public')

@section('main-content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">Contact Us</h1>
        
        @if($contact)
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Contact Information -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Get in Touch</h2>
                
                @if($contact->phone)
                <div class="mb-4 flex items-start">
                    <i class="fas fa-phone text-purple-600 text-xl mr-4 mt-1"></i>
                    <div>
                        <p class="font-semibold text-gray-700">Phone</p>
                        <a href="tel:{{ $contact->phone }}" class="text-purple-600 hover:underline">{{ $contact->phone }}</a>
                    </div>
                </div>
                @endif

                @if($contact->email)
                <div class="mb-4 flex items-start">
                    <i class="fas fa-envelope text-purple-600 text-xl mr-4 mt-1"></i>
                    <div>
                        <p class="font-semibold text-gray-700">Email</p>
                        @if($contact->google_form_url)
                        <a href="{{ $contact->google_form_url }}" target="_blank" class="text-purple-600 hover:underline flex items-center">
                            {{ $contact->email }}
                            <i class="fas fa-external-link-alt text-xs ml-2"></i>
                        </a>
                        <p class="text-xs text-gray-500 mt-1">Click to fill out our contact form</p>
                        @else
                        <a href="mailto:{{ $contact->email }}" class="text-purple-600 hover:underline">{{ $contact->email }}</a>
                        @endif
                    </div>
                </div>
                @endif

                @if($contact->address)
                <div class="mb-4 flex items-start">
                    <i class="fas fa-map-marker-alt text-purple-600 text-xl mr-4 mt-1"></i>
                    <div>
                        <p class="font-semibold text-gray-700">Address</p>
                        <p class="text-gray-600">{{ $contact->address }}</p>
                    </div>
                </div>
                @endif

                @if($contact->business_hours)
                <div class="mb-4 flex items-start">
                    <i class="fas fa-clock text-purple-600 text-xl mr-4 mt-1"></i>
                    <div>
                        <p class="font-semibold text-gray-700">Business Hours</p>
                        <p class="text-gray-600">{{ $contact->business_hours }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Social Media & Messaging -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Connect With Us</h2>
                
                @if($contact->whatsapp)
                <a href="https://wa.me/{{ $contact->whatsapp }}" target="_blank" class="block mb-4 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                    <div class="flex items-center">
                        <i class="fab fa-whatsapp text-green-600 text-3xl mr-4"></i>
                        <div>
                            <p class="font-semibold text-gray-700">WhatsApp</p>
                            <p class="text-gray-600">Chat with us instantly</p>
                        </div>
                    </div>
                </a>
                @endif

                @if($contact->facebook)
                <a href="{{ $contact->facebook }}" target="_blank" class="block mb-4 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                    <div class="flex items-center">
                        <i class="fab fa-facebook text-blue-600 text-3xl mr-4"></i>
                        <div>
                            <p class="font-semibold text-gray-700">Facebook Page</p>
                            <p class="text-gray-600">Visit our Facebook page</p>
                        </div>
                    </div>
                </a>
                @endif

                @if($contact->facebook)
                <a href="{{ $contact->facebook }}" target="_blank" class="block mb-4 p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                    <div class="flex items-center">
                        <i class="fab fa-facebook text-indigo-600 text-3xl mr-4"></i>
                        <div>
                            <p class="font-semibold text-gray-700">Facebook</p>
                            <p class="text-gray-600">Follow our page</p>
                        </div>
                    </div>
                </a>
                @endif
            </div>
        </div>
        @else
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
            <p class="text-gray-600">Contact information will be available soon.</p>
        </div>
        @endif
    </div>
</div>
@endsection
