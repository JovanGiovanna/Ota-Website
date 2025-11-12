<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Konfirmasi Pemesanan #' . $this->booking->booking_code)
            ->greeting('Halo ' . $this->booking->booker_name . '!')
            ->line('Terima kasih telah melakukan pemesanan.')
            ->line('Kode Booking: ' . $this->booking->booking_code)
            ->line('Total Harga: Rp ' . number_format($this->booking->total_price, 0, ',', '.'))
            ->action('Lihat Riwayat Booking', url('/user/history'))
            ->line('Terima kasih telah mempercayai layanan kami!');
    }
}
