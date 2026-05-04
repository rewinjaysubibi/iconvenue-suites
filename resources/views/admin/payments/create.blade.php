@extends('layouts.admin')

@section('page-title', 'Record Payment')

@section('main-content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-xl font-bold mb-4 text-gray-800">Booking Information</h3>
    <div class="grid md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
        <div>
            <p class="text-gray-600 text-sm">Client</p>
            <p class="font-semibold text-gray-800">{{ $booking->client_name }}</p>
        </div>
        <div>
            <p class="text-gray-600 text-sm">Venue</p>
            <p class="font-semibold text-gray-800">{{ $booking->venue->name }}</p>
        </div>
        <div>
            <p class="text-gray-600 text-sm">Total Amount</p>
            <p class="font-semibold text-gray-800">₱{{ number_format($booking->total_amount, 2) }}</p>
        </div>
        <div>
            <p class="text-gray-600 text-sm">Paid Amount</p>
            <p class="font-semibold text-green-600">₱{{ number_format($booking->payments->where('status', 'verified')->sum('amount'), 2) }}</p>
        </div>
        <div>
            <p class="text-gray-600 text-sm">Balance</p>
            <p class="font-semibold text-red-600">₱{{ number_format($booking->total_amount - $booking->payments->where('status', 'verified')->sum('amount'), 2) }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-xl font-bold mb-4 text-gray-800">Payment Details</h3>
    
    <form action="{{ route('admin.payments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
        
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Amount *</label>
                <input type="number" name="amount" value="{{ old('amount') }}" required min="0" step="0.01"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600"
                    placeholder="0.00">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Payment Method</label>
                <select name="payment_method" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                    <option value="">Select Method</option>
                    <option value="Cash">Cash</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="GCash">GCash</option>
                    <option value="PayMaya">PayMaya</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Reference Number</label>
                <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600"
                    placeholder="Transaction/Reference Number">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Payment Proof (Image)</label>
                <input type="file" name="proof_image" accept="image/*" id="proofImageInput"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600"
                    onchange="previewPaymentImage(event)">
                @error('proof_image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Upload receipt, screenshot, or payment confirmation (Max: 2MB)</p>
                
                <!-- Image Preview -->
                <div id="paymentImagePreview" class="mt-4 hidden">
                    <p class="text-sm text-gray-600 mb-2">Preview:</p>
                    <img id="paymentPreview" class="max-w-full h-48 object-contain rounded-lg shadow border">
                </div>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-gray-700 font-semibold mb-2">Notes</label>
            <textarea name="notes" rows="4" 
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600"
                placeholder="Additional notes about this payment...">{{ old('notes') }}</textarea>
        </div>

        <div class="mt-8 flex space-x-4">
            <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-save mr-2"></i>Record Payment
            </button>
            <a href="{{ route('admin.bookings.show', $booking) }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
        <div>
            <p class="font-semibold text-blue-800 mb-2">Payment Recording Tips:</p>
            <ul class="text-blue-700 text-sm space-y-1">
                <li>• Record the exact amount received from the client</li>
                <li>• Upload a clear image of the payment proof (screenshot, receipt, etc.)</li>
                <li>• Include the transaction reference number if available</li>
                <li>• Payment will be marked as "Pending" until verified</li>
                <li>• You can verify the payment after recording it</li>
            </ul>
        </div>
    </div>
</div>

<script>
function previewPaymentImage(event) {
    const file = event.target.files[0];
    if (file) {
        // Validate file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            event.target.value = '';
            document.getElementById('paymentImagePreview').classList.add('hidden');
            return;
        }
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image file');
            event.target.value = '';
            document.getElementById('paymentImagePreview').classList.add('hidden');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('paymentPreview').src = e.target.result;
            document.getElementById('paymentImagePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('paymentImagePreview').classList.add('hidden');
    }
}
</script>
@endsection
