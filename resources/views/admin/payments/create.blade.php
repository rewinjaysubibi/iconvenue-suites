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
            <p class="font-semibold text-green-600">₱{{ number_format($paidAmount, 2) }}</p>
        </div>
        <div>
            <p class="text-gray-600 text-sm">Balance</p>
            <p class="font-semibold text-red-600">₱{{ number_format($balance, 2) }}</p>
        </div>
    </div>
</div>

@if($balance <= 0)
<div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
    <i class="fas fa-check-circle text-green-600 text-3xl mb-3"></i>
    <p class="font-semibold text-green-800 text-lg">This booking is fully paid.</p>
    <a href="{{ route('admin.bookings.show', $booking) }}" class="inline-block mt-4 bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
        Back to Booking
    </a>
</div>
@else
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-xl font-bold mb-4 text-gray-800">Payment Details</h3>
    
    <form action="{{ route('admin.payments.store') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
        @csrf
        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
        
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-3">Payment Type *</label>
            <div class="grid md:grid-cols-2 gap-4">
                <label class="payment-type-option cursor-pointer border-2 rounded-lg p-4 transition-all {{ old('payment_type', 'full') === 'full' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300' }}" data-type="full">
                    <input type="radio" name="payment_type" value="full" class="sr-only payment-type-radio" {{ old('payment_type', 'full') === 'full' ? 'checked' : '' }}>
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-bolt text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Full Payment</p>
                            <p class="text-sm text-gray-600 mt-1">Pay remaining balance in one step</p>
                            <p class="text-sm font-semibold text-green-700 mt-2">₱{{ number_format($balance, 2) }}</p>
                            <span class="inline-block mt-2 px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Fast transaction — auto-verified</span>
                        </div>
                    </div>
                </label>

                <label class="payment-type-option cursor-pointer border-2 rounded-lg p-4 transition-all {{ old('payment_type') === 'partial' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-purple-300' }}" data-type="partial">
                    <input type="radio" name="payment_type" value="partial" class="sr-only payment-type-radio" {{ old('payment_type') === 'partial' ? 'checked' : '' }}>
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-coins text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Partial Payment</p>
                            <p class="text-sm text-gray-600 mt-1">Record a custom amount</p>
                            <p class="text-sm text-gray-500 mt-2">Pending until manually verified</p>
                        </div>
                    </div>
                </label>
            </div>
            @error('payment_type')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Amount *</label>
                <input type="number" name="amount" id="paymentAmount" value="{{ old('amount', old('payment_type', 'full') === 'partial' ? '' : number_format($balance, 2, '.', '')) }}" required min="0.01" max="{{ number_format($balance, 2, '.', '') }}" step="0.01"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600"
                    placeholder="0.00">
                @error('amount')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p id="amountHint" class="text-sm text-gray-500 mt-1">Full balance will be paid and verified immediately.</p>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Payment Method</label>
                <select name="payment_method" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                    <option value="">Select Method</option>
                    <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="GCash" {{ old('payment_method') === 'GCash' ? 'selected' : '' }}>GCash</option>
                    <option value="PayMaya" {{ old('payment_method') === 'PayMaya' ? 'selected' : '' }}>PayMaya</option>
                    <option value="Credit Card" {{ old('payment_method') === 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                    <option value="Other" {{ old('payment_method') === 'Other' ? 'selected' : '' }}>Other</option>
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
            <button type="submit" id="submitPaymentBtn" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-bolt mr-2"></i>Record Full Payment
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
                <li>• Use <strong>Full Payment</strong> for walk-in or on-the-spot payments — it verifies and confirms the booking in one step</li>
                <li>• Use <strong>Partial Payment</strong> when the client pays in installments — verify each payment after review</li>
                <li>• Upload a clear image of the payment proof when available</li>
                <li>• Include the transaction reference number if available</li>
            </ul>
        </div>
    </div>
</div>

<script>
const paymentBalance = {{ number_format($balance, 2, '.', '') }};

function previewPaymentImage(event) {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            event.target.value = '';
            document.getElementById('paymentImagePreview').classList.add('hidden');
            return;
        }
        
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

function updatePaymentTypeUI(type) {
    const amountInput = document.getElementById('paymentAmount');
    const amountHint = document.getElementById('amountHint');
    const submitBtn = document.getElementById('submitPaymentBtn');
    const options = document.querySelectorAll('.payment-type-option');

    options.forEach(option => {
        const isActive = option.dataset.type === type;
        option.classList.remove('border-green-500', 'bg-green-50', 'border-purple-500', 'bg-purple-50', 'border-gray-200');
        if (isActive) {
            option.classList.add(type === 'full' ? 'border-green-500' : 'border-purple-500');
            option.classList.add(type === 'full' ? 'bg-green-50' : 'bg-purple-50');
        } else {
            option.classList.add('border-gray-200');
        }
    });

    if (type === 'full') {
        amountInput.value = paymentBalance.toFixed(2);
        amountInput.readOnly = true;
        amountInput.classList.add('bg-gray-100');
        amountHint.textContent = 'Full balance will be paid and verified immediately.';
        submitBtn.className = 'bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition';
        submitBtn.innerHTML = '<i class="fas fa-bolt mr-2"></i>Record Full Payment';
    } else {
        amountInput.readOnly = false;
        amountInput.classList.remove('bg-gray-100');
        if (parseFloat(amountInput.value) === paymentBalance) {
            amountInput.value = '';
        }
        amountHint.textContent = 'Enter the amount received. Payment stays pending until verified.';
        submitBtn.className = 'bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition';
        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Record Payment';
    }
}

document.querySelectorAll('.payment-type-option').forEach(option => {
    option.addEventListener('click', function() {
        const radio = this.querySelector('.payment-type-radio');
        radio.checked = true;
        updatePaymentTypeUI(this.dataset.type);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const selected = document.querySelector('.payment-type-radio:checked');
    updatePaymentTypeUI(selected ? selected.value : 'full');
});
</script>
@endif
@endsection
