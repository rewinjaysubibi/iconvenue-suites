<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use App\Mail\PaymentStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['booking.venue', 'verifiedBy'])
            ->latest()
            ->paginate(15);
        return view('admin.payments.index', compact('payments'));
    }

    public function create(Booking $booking)
    {
        return view('admin.payments.create', compact('booking'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'notes' => 'nullable|string|max:1000'
        ], [
            'amount.min' => 'Payment amount must be greater than zero.',
            'proof_image.image' => 'Payment proof must be a valid image file.',
            'proof_image.mimes' => 'Payment proof must be a JPEG, PNG, JPG, GIF, or WebP image.',
            'proof_image.max' => 'Payment proof image must not exceed 2MB.',
        ]);

        try {
            if ($request->hasFile('proof_image')) {
                $file = $request->file('proof_image');
                
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $storedPath = $file->storeAs('payments', $filename, 'public');
                $validated['proof_image'] = $storedPath;

                // Copy to public/storage for web access (Windows XAMPP symlink workaround)
                $sourcePath = storage_path('app/public/' . $storedPath);
                $publicPath = public_path('storage/' . $storedPath);
                if (!is_dir(dirname($publicPath))) mkdir(dirname($publicPath), 0755, true);
                if (file_exists($sourcePath) && !file_exists($publicPath)) copy($sourcePath, $publicPath);
            }

            $validated['status'] = 'pending';

            $payment = Payment::create($validated);

            return redirect()->route('admin.bookings.show', $validated['booking_id'])
                ->with('success', 'Payment record created successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Payment creation failed: ' . $e->getMessage());
            
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create payment record. Please try again.']);
        }
    }

    public function verify(Payment $payment)
    {
        $payment->update([
            'status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now()
        ]);

        $booking = $payment->booking;
        $totalPaid = $booking->payments()->where('status', 'verified')->sum('amount');

        if ($totalPaid >= $booking->total_amount) {
            // Fully paid — confirm the booking automatically
            $booking->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
            ]);
        } else {
            // Partial payment — keep pending, update payment status
            $booking->update(['payment_status' => 'partial']);
        }

        // Load relationships for email
        $payment->load(['booking.venue', 'booking.package']);

        // Send email notification to client
        try {
            Mail::to($payment->booking->client_email)->send(new PaymentStatusNotification($payment, 'verified'));
        } catch (\Exception $e) {
            \Log::error('Failed to send payment verified email: ' . $e->getMessage());
        }

        $message = $totalPaid >= $booking->total_amount
            ? 'Payment verified! Booking has been automatically confirmed.'
            : 'Payment verified! Booking remains pending until full payment is received.';

        return back()->with('success', $message);
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'notes' => 'required|string'
        ]);

        $payment->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'notes' => $request->notes
        ]);

        // Load relationships for email
        $payment->load(['booking.venue', 'booking.package']);

        // Send email notification to client
        try {
            Mail::to($payment->booking->client_email)->send(new PaymentStatusNotification($payment, 'rejected'));
        } catch (\Exception $e) {
            \Log::error('Failed to send payment rejected email: ' . $e->getMessage());
        }

        return back()->with('success', 'Payment rejected! Email notification sent to client.');
    }

    public function destroy(Payment $payment)
    {
        // Only allow deletion of rejected payments
        if ($payment->status !== 'rejected') {
            return back()->withErrors(['error' => 'Only rejected payments can be deleted.']);
        }

        $payment->delete();

        return back()->with('success', 'Payment record deleted successfully!');
    }
}
