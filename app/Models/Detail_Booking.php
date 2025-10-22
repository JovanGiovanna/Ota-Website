<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 

class Detail_Booking extends Model
{
    use HasFactory;

    protected $table = 'detail_booking';

    protected $fillable = [
        'booking_id',
        'product_id',
        'booker_name',
        'adults',
        'children',
        'special_request',
    ];

    public function booking(): BelongsTo 
    {
        return $this->belongsTo(Booking::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}