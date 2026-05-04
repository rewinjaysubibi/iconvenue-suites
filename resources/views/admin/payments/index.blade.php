@extends('layouts.admin')

@section('page-title', 'Payment Management')

@section('main-content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($payments as $payment)
            <tr>
                <td class="px-6 py-4 text-gray-600">#{{ $payment->id }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.bookings.show', $payment->booking) }}" class="text-purple-600 hover:underline">
                        Booking #{{ $payment->booking_id }}
                    </a>
                </td>
                <td class="px-6 py-4">
                    <div class="font-semibold text-gray-800">{{ $payment->booking->client_name }}</div>
                    <div class="text-sm text-gray-600">{{ $payment->booking->venue->name }}</div>
                </td>
                <td class="px-6 py-4 text-gray-600 font-semibold">₱{{ number_format($payment->amount, 2) }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $payment->payment_method ?? 'N/A' }}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($payment->status == 'verified') bg-green-100 text-green-800
                        @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-600">
                    <div>{{ $payment->created_at->format('M d, Y') }}</div>
                    <div class="text-xs text-gray-400">{{ $payment->created_at->format('h:i A') }}</div>
                    @if($payment->verified_at)
                    <div class="text-xs text-gray-400 mt-1">
                        {{ $payment->status == 'verified' ? 'Verified' : 'Rejected' }}: {{ $payment->verified_at->format('M d, Y h:i A') }}
                    </div>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-2">
                        <!-- View Booking Button -->
                        <a href="{{ route('admin.bookings.show', $payment->booking) }}" 
                           class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                           title="View Booking">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        
                        <!-- View Proof Image -->
                        @if($payment->proof_image)
                        <a href="{{ asset('storage/' . $payment->proof_image) }}" 
                           target="_blank" 
                           class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                           title="View Proof">
                            <i class="fas fa-image mr-1"></i>Proof
                        </a>
                        @endif
                        
                        <!-- Verify Button (Pending Only) -->
                        @if($payment->status == 'pending')
                        <form action="{{ route('admin.payments.verify', $payment) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                                    title="Verify Payment"
                                    onclick="return confirm('Verify this payment?')">
                                <i class="fas fa-check-circle mr-1"></i>Verify
                            </button>
                        </form>
                        
                        <!-- Reject Button (Pending Only) -->
                        <button onclick="showRejectModal({{ $payment->id }})" 
                                class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                                title="Reject Payment">
                            <i class="fas fa-times-circle mr-1"></i>Reject
                        </button>
                        @endif
                        
                        <!-- Delete Button (Rejected Only) -->
                        @if($payment->status == 'rejected')
                        <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                                    title="Delete Payment"
                                    onclick="return confirm('Permanently delete this rejected payment? This action cannot be undone.')">
                                <i class="fas fa-trash-alt mr-1"></i>Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-600">No payment records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $payments->links() }}
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-4 text-gray-800">Reject Payment</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Reason for Rejection *</label>
                <textarea name="notes" rows="4" required 
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600"
                    placeholder="Please provide a reason for rejecting this payment..."></textarea>
            </div>
            <div class="flex space-x-4">
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                    Reject Payment
                </button>
                <button type="button" onclick="closeRejectModal()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showRejectModal(paymentId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = `/admin/payments/${paymentId}/reject`;
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
}
</script>
@endpush
@endsection
