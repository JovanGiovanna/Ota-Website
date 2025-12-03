<?php

namespace App\Helpers;

class SweetAlertHelper
{
    /**
     * Generate SweetAlert2 success notification
     */
    public static function success($title, $message = '', $icon = 'success')
    {
        return response()->json([
            'status' => 'success',
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
        ]);
    }

    /**
     * Generate SweetAlert2 error notification
     */
    public static function error($title, $message = '', $icon = 'error')
    {
        return response()->json([
            'status' => 'error',
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
        ]);
    }

    /**
     * Generate SweetAlert2 warning notification
     */
    public static function warning($title, $message = '', $icon = 'warning')
    {
        return response()->json([
            'status' => 'warning',
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
        ]);
    }

    /**
     * Generate SweetAlert2 info notification
     */
    public static function info($title, $message = '', $icon = 'info')
    {
        return response()->json([
            'status' => 'info',
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
        ]);
    }

    /**
     * Generate SweetAlert2 loading/processing notification
     */
    public static function loading($title = 'Processing...', $message = '')
    {
        return response()->json([
            'status' => 'loading',
            'title' => $title,
            'message' => $message,
            'icon' => 'info',
            'allowOutsideClick' => false,
            'allowEscapeKey' => false,
            'didOpen' => 'showLoadingAnimation',
        ]);
    }

    /**
     * Generate SweetAlert2 confirmation dialog
     */
    public static function confirm($title, $message = '', $confirmText = 'Yes', $cancelText = 'No')
    {
        return response()->json([
            'status' => 'confirm',
            'title' => $title,
            'message' => $message,
            'icon' => 'question',
            'confirmButtonText' => $confirmText,
            'cancelButtonText' => $cancelText,
            'showCancelButton' => true,
        ]);
    }

    /**
     * Generate SweetAlert2 success with redirect
     */
    public static function successWithRedirect($title, $message, $redirectUrl, $delay = 1500)
    {
        return response()->json([
            'status' => 'success',
            'title' => $title,
            'message' => $message,
            'icon' => 'success',
            'redirect' => $redirectUrl,
            'delay' => $delay,
        ]);
    }

    /**
     * Generate SweetAlert2 created notification
     */
    public static function created($resourceName = 'Data')
    {
        return self::success(
            'Success!',
            $resourceName . ' created successfully.',
            'success'
        );
    }

    /**
     * Generate SweetAlert2 updated notification
     */
    public static function updated($resourceName = 'Data')
    {
        return self::success(
            'Success!',
            $resourceName . ' updated successfully.',
            'success'
        );
    }

    /**
     * Generate SweetAlert2 deleted notification
     */
    public static function deleted($resourceName = 'Data')
    {
        return self::success(
            'Deleted!',
            $resourceName . ' has been deleted.',
            'success'
        );
    }

    /**
     * Generate SweetAlert2 registered notification
     */
    public static function registered($message = 'Registration successful! Redirecting to login page...')
    {
        return self::successWithRedirect(
            'Welcome!',
            $message,
            route('login'),
            2000
        );
    }

    /**
     * Generate SweetAlert2 logged in notification
     */
    public static function loggedIn($message = 'Login successful! Redirecting to dashboard...', $redirectUrl = null)
    {
        return self::successWithRedirect(
            'Welcome Back!',
            $message,
            $redirectUrl ?? route('dashboard'),
            1500
        );
    }

    /**
     * Generate SweetAlert2 logged out notification
     */
    public static function loggedOut($message = 'Logged out successfully!')
    {
        return self::success(
            'Goodbye!',
            $message,
            'success'
        );
    }

    /**
     * Generate SweetAlert2 validation error
     */
    public static function validationError($message = 'Please check all fields')
    {
        return self::error(
            'Validation Error',
            $message,
            'error'
        );
    }

    /**
     * Generate SweetAlert2 unauthorized error
     */
    public static function unauthorized($message = 'You are not authorized to perform this action')
    {
        return self::error(
            'Unauthorized',
            $message,
            'error'
        );
    }

    /**
     * Generate SweetAlert2 not found error
     */
    public static function notFound($resourceName = 'Resource')
    {
        return self::error(
            'Not Found',
            $resourceName . ' not found.',
            'error'
        );
    }

    /**
     * Convert validation errors to array for SweetAlert
     */
    public static function getValidationErrors($validator)
    {
        $errors = $validator->errors()->all();
        return implode('<br>', $errors);
    }
}
