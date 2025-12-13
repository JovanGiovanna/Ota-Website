<?php

namespace App\Helpers;

use RealRashid\SweetAlert\Facades\Alert;

/**
 * Global alert helper function to simplify SweetAlert2 usage
 */
if (!function_exists('alert')) {
    function alert()
    {
        return Alert::class;
    }
}
