<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use App\Models\{Package, Addon, Booking};
use App\Models\BookAddon;

class PackageAddonBookingCodeUniquenessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function package_addons_create_unique_booking_codes()
    {
        // Create two addons used by the package
        $addon1 = Addon::create([
            'addons' => 'Comfort Travel Pillow',
            'pax' => 1,
            'location' => 'Yogyakarta',
            'phone' => '0800000001',
            'basic_price' => 100000,
            'nta' => 100000,
            'status' => 'available',
        ]);

        $addon2 = Addon::create([
            'addons' => 'Comfort Eye Mask',
            'pax' => 1,
            'location' => 'Yogyakarta',
            'phone' => '0800000002',
            'basic_price' => 50000,
            'nta' => 50000,
            'status' => 'available',
        ]);

        // Create a package that includes the two addons
        $package = Package::create([
            'name_package' => 'Liburan Jogja',
            'slug' => 'liburan-jogja-' . Str::random(6),
            'description' => 'Test package',
            'images' => [],
            'location' => 'Yogyakarta',
            'phone' => '081234567890',
            'nta' => 250000,
            'products_data' => [],
            'addons_data' => [
                ['id' => $addon1->id, 'pax' => 1],
                ['id' => $addon2->id, 'pax' => 1],
            ],
            'pax_paid' => 250000,
            'start_publish' => now()->subDay(),
            'end_publish' => null,
            'is_active' => true,
        ]);

        // Perform booking with only the package
        $response = $this->post('/book', [
            'booking_types' => ['package'],
            'id_package' => [$package->id],
            'quantity' => [1],
            'booker_name' => 'Tester',
            'booker_email' => 'tester@example.com',
            'booker_telp' => '0811111111',
            'checkin_appointment_start' => now()->addDay()->format('Y-m-d H:i:s'),
            'checkout_appointment_end' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'duration_days' => 1,
            'amount' => 1,
            'requests' => null,
        ]);

        $response->assertStatus(302); // redirects after booking

        // Fetch the booking created
        $booking = Booking::latest('created_at')->first();
        $this->assertNotNull($booking, 'Booking should be created');

        // Fetch package-created addons for this booking
        $addons = BookAddon::where('id_book', $booking->id)
            ->where('notes', 'like', 'From package:%')
            ->get();

        $this->assertCount(2, $addons, 'Two package-created addons should exist');

        // Ensure booking_code values are unique
        $codes = $addons->pluck('booking_code')->filter()->values();
        $this->assertEquals(2, $codes->count(), 'Each addon should have a booking_code');
        $this->assertEquals($codes->unique()->count(), $codes->count(), 'booking_code values must be unique per addon');

        // Ensure they share the base booking code prefix
        $base = $booking->booking_code;
        foreach ($codes as $code) {
            $this->assertStringStartsWith($base . '-', $code);
        }
    }
}
