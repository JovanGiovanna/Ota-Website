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
        $mail = (new MailMessage)
            ->subject('Konfirmasi Pemesanan #' . $this->booking->booking_code)
            ->greeting('Halo ' . $this->booking->booker_name . '!')
            ->line('Terima kasih telah melakukan pemesanan.')
            ->line('Kode Booking: ' . $this->booking->booking_code)
            ->line('Total Harga: Rp ' . number_format($this->booking->total_price, 0, ',', '.'))
            ->action('Lihat Detail Booking', url('/user/history/' . $this->booking->id))
            ->line('Terima kasih telah mempercayai layanan kami!');

        // If user is a guest (has activation token), add activation link
        if ($notifiable->activation_token && is_null($notifiable->password)) {
            $mail->line('Untuk mengelola booking Anda di masa depan, silakan lengkapi akun Anda:')
                ->action('Aktivasi Akun', url('/account/activate/' . $notifiable->activation_token));
        }

        return $mail;
    }
}
