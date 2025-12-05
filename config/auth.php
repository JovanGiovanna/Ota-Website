    <?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],


    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'vendor' => [
            'driver' => 'session',
            'provider' => 'vendors',
        ],
        'super_admin' => [
            'driver' => 'session',
            'provider' => 'super_admins',
        ],
        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => 'users',
            'hash' => false,
        ],
        'sanctum_admin' => [
            'driver' => 'sanctum',
            'provider' => 'admins',
            'hash' => false,
        ],
        'sanctum_vendor' => [
            'driver' => 'sanctum',
            'provider' => 'vendors',
            'hash' => false,
        ],
        'sanctum_super_admin' => [
            'driver' => 'sanctum',
            'provider' => 'super_admins',
            'hash' => false,
        ],
    ],


    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        // Tambahkan provider 'vendors' di sini:
        'vendors' => [
            'driver' => 'eloquent',
            'model' => App\Models\Vendor::class, // Merujuk ke Model Vendor yang menggunakan UUID
        ],
        'super_admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\SuperAdmin::class, // Arahkan ke model SuperAdmin
        ],
    ],


    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
