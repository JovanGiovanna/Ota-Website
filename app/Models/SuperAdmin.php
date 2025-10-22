<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids; // Import trait untuk UUID
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SuperAdmin extends Authenticatable
{
    use HasApiTokens, HasUuids, Notifiable; // Tambahkan HasUuids

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'super_admin'; // Penting: Sesuaikan dengan nama tabel di migrasi

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Atribut yang harus disembunyikan untuk serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-cast ke tipe bawaan.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // Tidak perlu cast 'id' ke string/uuid karena sudah diurus oleh HasUuids
    ];

    /**
     * Set `primaryKey` menjadi string karena menggunakan UUID.
     *
     * @var string
     */
    protected $keyType = 'string';
    
    /**
     * Nonaktifkan auto increment karena menggunakan UUID.
     *
     * @var bool
     */
    public $incrementing = false;
}