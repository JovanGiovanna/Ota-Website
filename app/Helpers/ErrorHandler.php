<?php

namespace App\Helpers;

use Illuminate\Validation\Validator;

class ErrorHandler
{
    /**
     * Handle validation errors dengan SweetAlert untuk AJAX/JSON responses
     */
    public static function validationErrorJson(Validator $validator, $statusCode = 422)
    {
        $errors = $validator->errors()->all();
        $errorMessage = implode('<br>', $errors);

        return response()->json([
            'status' => 'error',
            'title' => 'Validasi Gagal',
            'message' => $errorMessage,
            'icon' => 'error',
            'errors' => $validator->errors(),
        ], $statusCode);
    }

    /**
     * Handle "akun tidak ditemukan" error
     */
    public static function accountNotFound($redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();

        if (function_exists('alert')) {
            alert()->error('Akun Tidak Ditemukan', 'Tidak ada akun terdaftar dengan email ini.');
        }

        return $redirect->withInput();
    }

    /**
     * Handle "email sudah terdaftar" error
     */
    public static function emailAlreadyRegistered($redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();

        if (function_exists('alert')) {
            alert()->error('Email Sudah Terdaftar', 'Email ini sudah terdaftar. Silakan login atau gunakan email lain.');
        }

        return $redirect->withInput();
    }

    /**
     * Handle "password salah" error
     */
    public static function invalidCredentials($redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();

        if (function_exists('alert')) {
            alert()->error('Login Gagal', 'Email atau password salah. Silakan coba lagi.');
        }

        return $redirect->withInput();
    }

    /**
     * Handle "unauthorized" error
     */
    public static function unauthorized($message = 'Anda tidak memiliki akses untuk melakukan aksi ini.', $redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();

        if (function_exists('alert')) {
            alert()->error('Akses Ditolak', $message);
        }

        return $redirect;
    }

    /**
     * Handle "resource tidak ditemukan" error
     */
    public static function resourceNotFound($resourceName = 'Data', $redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();

        if (function_exists('alert')) {
            alert()->error('Tidak Ditemukan', $resourceName . ' tidak ditemukan.');
        }

        return $redirect;
    }

    /**
     * Handle generic error
     */
    public static function genericError($message = 'Terjadi kesalahan. Silakan coba lagi.', $redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();

        if (function_exists('alert')) {
            alert()->error('Error', $message);
        }

        return $redirect->withInput();
    }

    /**
     * Handle server error (500)
     */
    public static function serverError($message = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.', $redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();

        if (function_exists('alert')) {
            alert()->error('Server Error', $message);
        }

        return $redirect->withInput();
    }

    /**
     * Handle operation failed error
     */
    public static function operationFailed($operationName = 'Operasi', $message = null, $redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();
        $errorMessage = $message ?? $operationName . ' gagal. Silakan coba lagi.';

        if (function_exists('alert')) {
            alert()->error($operationName . ' Gagal', $errorMessage);
        }

        return $redirect->withInput();
    }

    /**
     * Handle duplicate data error
     */
    public static function duplicateData($fieldName = 'Data', $redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();

        if (function_exists('alert')) {
            alert()->error('Data Duplikat', $fieldName . ' sudah ada. Silakan gunakan yang berbeda.');
        }

        return $redirect->withInput();
    }

    /**
     * Handle status conflict error
     */
    public static function statusConflict($message = 'Aksi tidak dapat dilakukan pada status saat ini.', $redirectUrl = null)
    {
        $redirect = $redirectUrl ? redirect($redirectUrl) : back();

        if (function_exists('alert')) {
            alert()->error('Status Tidak Valid', $message);
        }

        return $redirect;
    }

    /**
     * Handle validation error untuk JSON response (dari SweetAlertHelper)
     */
    public static function validationErrorResponse(Validator $validator, $statusCode = 422)
    {
        $errorMessage = SweetAlertHelper::getValidationErrors($validator);

        return response()->json([
            'status' => 'error',
            'title' => 'Validasi Gagal',
            'message' => $errorMessage,
            'icon' => 'error',
            'errors' => $validator->errors(),
        ], $statusCode);
    }

    /**
     * Format validation errors untuk ditampilkan
     */
    public static function formatValidationErrors(Validator $validator, $separator = '<br>')
    {
        return implode($separator, $validator->errors()->all());
    }

    /**
     * Get first validation error
     */
    public static function getFirstValidationError(Validator $validator)
    {
        $errors = $validator->errors()->all();
        return !empty($errors) ? $errors[0] : 'Validasi gagal';
    }
}
