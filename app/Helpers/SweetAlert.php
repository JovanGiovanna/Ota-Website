<?php

namespace App\Helpers;

class SweetAlert
{
    /**
     * Redirect dengan success notification (untuk form tradisional)
     * 
     * Usage: return redirect()->route('home')->with('sweet_success', 'Data berhasil disimpan!');
     */
    public static function success($message, $redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();
        // If realrashid/sweet-alert is available, use its helper to queue the alert
        if (function_exists('alert')) {
            alert()->success('Success', $message);
            return $redirect;
        }
        return $redirect->with('sweet_success', $message);
    }

    /**
     * Redirect dengan error notification
     * Usage: return redirect()->back()->with('sweet_error', 'Data gagal disimpan!');
     */
    public static function error($message, $redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();
        if (function_exists('alert')) {
            alert()->error('Error', $message);
            return $redirect;
        }
        return $redirect->with('sweet_error', $message);
    }

    /**
     * Redirect dengan warning notification
     */
    public static function warning($message, $redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();
        if (function_exists('alert')) {
            alert()->warning('Warning', $message);
            return $redirect;
        }
        return $redirect->with('sweet_warning', $message);
    }

    /**
     * Redirect dengan info notification
     */
    public static function info($message, $redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();
        if (function_exists('alert')) {
            alert()->info('Information', $message);
            return $redirect;
        }
        return $redirect->with('sweet_info', $message);
    }

    /**
     * Khusus untuk notifikasi created (dengan custom resource name)
     * Usage: return SweetAlert::created('Vendor', route('vendors.index'));
     */
    public static function created($resourceName = 'Data', $redirectUrl = null)
    {
        $message = $resourceName . ' berhasil dibuat!';
        return self::success($message, $redirectUrl ?? back());
    }

    /**
     * Khusus untuk notifikasi updated
     */
    public static function updated($resourceName = 'Data', $redirectUrl = null)
    {
        $message = $resourceName . ' berhasil diubah!';
        return self::success($message, $redirectUrl ?? back());
    }

    /**
     * Khusus untuk notifikasi deleted
     */
    public static function deleted($resourceName = 'Data', $redirectUrl = null)
    {
        $message = $resourceName . ' berhasil dihapus!';
        return self::success($message, $redirectUrl ?? back());
    }

    /**
     * Khusus untuk error create
     */
    public static function createFailed($resourceName = 'Data')
    {
        return self::error($resourceName . ' gagal dibuat!');
    }

    /**
     * Khusus untuk error update
     */
    public static function updateFailed($resourceName = 'Data')
    {
        return self::error($resourceName . ' gagal diubah!');
    }

    /**
     * Khusus untuk error delete
     */
    public static function deleteFailed($resourceName = 'Data')
    {
        return self::error($resourceName . ' gagal dihapus!');
    }
}
