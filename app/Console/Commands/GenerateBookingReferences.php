<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class GenerateBookingReferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:generate-references';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate booking reference codes for existing bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookings = Booking::whereNull('booking_reference')->get();
        
        if ($bookings->isEmpty()) {
            $this->info('All bookings already have reference codes.');
            return;
        }

        $this->info("Generating reference codes for {$bookings->count()} bookings...");

        foreach ($bookings as $booking) {
            $booking->update(['booking_reference' => Booking::generateBookingReference()]);
            $this->line("Generated reference for booking ID {$booking->id}: {$booking->booking_reference}");
        }

        $this->info('All booking reference codes generated successfully!');
    }
}
