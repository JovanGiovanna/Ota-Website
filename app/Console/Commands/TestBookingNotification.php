<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\EmailSetting;
use App\Mail\BookingNotificationToHeadbase;
use Illuminate\Support\Facades\Mail;

class TestBookingNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:booking-notification {booking_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test booking notification email to headbase';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookingId = $this->argument('booking_id');
        
        // If no booking ID provided, use the latest booking
        if (!$bookingId) {
            $booking = Booking::with(['user', 'packages', 'products', 'addons'])->latest()->first();
            if (!$booking) {
                $this->error('No bookings found in database. Please create a booking first.');
                return 1;
            }
            $this->info("Using latest booking: {$booking->booking_code}");
        } else {
            $booking = Booking::with(['user', 'packages', 'products', 'addons'])->find($bookingId);
            if (!$booking) {
                $this->error("Booking with ID {$bookingId} not found.");
                return 1;
            }
        }

        // Get active email settings
        $activeEmails = EmailSetting::where('is_active', true)->get();
        
        if ($activeEmails->isEmpty()) {
            $this->warn('No active email recipients configured!');
            $this->info('Please add email recipients at: /super-admin/email-settings');
            return 1;
        }

        $this->info("\nBooking Details:");
        $this->table(
            ['Field', 'Value'],
            [
                ['Booking Code', $booking->booking_code],
                ['Customer', $booking->user->name ?? 'N/A'],
                ['Total Price', 'Rp ' . number_format($booking->total_price, 0, ',', '.')],
                ['Status', $booking->status],
                ['Payment Status', $booking->payment_status],
            ]
        );

        $this->info("\nActive Email Recipients:");
        foreach ($activeEmails as $email) {
            $this->line("  - {$email->label} <{$email->email}>");
        }

        if (!$this->confirm("\nSend test email to these recipients?", true)) {
            $this->info('Test cancelled.');
            return 0;
        }

        $this->info("\nSending emails...");
        $bar = $this->output->createProgressBar(count($activeEmails));

        foreach ($activeEmails as $emailSetting) {
            try {
                Mail::to($emailSetting->email)->send(new BookingNotificationToHeadbase($booking));
                $this->newLine();
                $this->info("✓ Sent to: {$emailSetting->email}");
                $bar->advance();
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("✗ Failed to send to {$emailSetting->email}: " . $e->getMessage());
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Test completed!');
        $this->info('Check your email inbox and storage/logs/laravel.log for details.');

        return 0;
    }
}
