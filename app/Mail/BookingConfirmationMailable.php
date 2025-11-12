<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Booking;

class BookingConfirmationMailable extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $totalPrice = number_format($this->booking->total_price ?? 0, 0, ',', '.'); 
        $userName = $notifiable->name ?? 'Pelanggan';

        return (new MailMessage)
            ->subject('Konfirmasi Pemesanan Anda - #' . $this->booking->id)
            ->greeting("Halo, {$userName}!")
            ->line('Terima kasih! Pemesanan Anda telah berhasil dikonfirmasi.')
            ->line('Berikut detail pemesanan Anda:')
            ->line("No. Pemesanan: **#{$this->booking->booking_id}**")
            ->line("Tanggal Pemesanan: {$this->booking->created_at->format('d F Y H:i')}")
            ->line("Total Pembayaran: **Rp {$totalPrice}**")
            ->action('Lihat Detail Pemesanan', url('/user/bookings/' . $this->booking->id))
            ->line('Kami akan segera memproses pemesanan Anda. Silakan hubungi kami jika ada pertanyaan.')
            ->line('Salam hangat Pointer By Telkom.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'status' => 'confirmed',
        ];
    }
}
